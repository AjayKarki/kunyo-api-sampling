<?php

namespace App\Http\Controllers\Admin\Actions;

use Foundation\Lib\PaymentUtil;
use Modules\Payment\Libs\Payment;
use Illuminate\Support\Facades\DB;
use Modules\Payment\PaymentService;
use Modules\Payment\Gateway\NicAsia\NicAsiaConfig;

/**
 * Class VerifyNicAsiaOrder
 *
 * WEBHOOOK/ADDRESS/Verification
 *
 * @package App\Http\Controllers\Admin\Actions
 */
final class VerifyNicAsiaOrder
{

    /**
     * @TODO automate feature like hold / Assi
     * Sha 256 digest string
     *
     * @var string
     */
    protected $sha256digest = 'SHA-256=';
    protected $hmacsha256 = "HmacSHA256";
    protected $signature = "Signature:";
    protected $postalgoheader = "host date (request-target) digest v-c-merchant-id"; // HeaderString
    protected $sha256 = "sha256";

    public function __invoke($refCode)
    {

        $transaction    = app(PaymentService::class)->byTransactionIdentifier($refCode);

        if (is_null($transaction)) {
            flash('error', 'The payment status cannot be checked for the moment.');
            return back();
        }

        $host           = NicAsiaConfig::getHost() ?? 'https://apitest.cybersource.com/';
        $endpoint       = NicAsiaConfig::getVerifyEndpoint();
        $merchantConfig = VerifyNicAsiaOrder::config();

        $requestObjArr  = VerifyNicAsiaOrder::requestObjectArr($refCode);

        $signatureGeneration  = [
            'host'          => $host,
            'date'          => gmdate('D, d M Y H:i:s T'),
            'methodHeader'  => "post",
            'resourcePath'  => "/tss/v2/searches"
        ];

        try {
            $signatureGeneration['digest'] = $this->generateDigest(json_encode($requestObjArr, JSON_PRETTY_PRINT));
            $signatureString = "host: ".$signatureGeneration['host']."\ndate: ".$signatureGeneration['date']."\n(request-target): ".$signatureGeneration['methodHeader']." ".$signatureGeneration['resourcePath']."\ndigest: ".$this->sha256digest.$signatureGeneration['digest']."\nv-c-merchant-id: ".$merchantConfig['id'];

            $token = $this->generateSignatureTokenHeader($signatureString, $this->postalgoheader, $merchantConfig);
            $headers = explode(PHP_EOL, $signatureString);
            $headers = array_merge($headers, [$token, "Content-Type: application/json"]);

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $endpoint,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($requestObjArr, JSON_PRETTY_PRINT),
                CURLOPT_HTTPHEADER => $headers,
            ]);

            $response = curl_exec($curl);
            curl_close($curl);

            \Log::error('NIC Response before decoded', [
                'response' => $response,
                'request'  => $requestObjArr,
                'signature' => $signatureString,
                'post_field' => json_encode($requestObjArr, JSON_PRETTY_PRINT),
                'header'    => $headers,
            ]);

            $response = json_decode($response, true);

            \Log::error('NIC ERROR', [
                'response' => $response,
                'signature' => $signatureGeneration,
                'endpoint'  => NicAsiaConfig::getVerifyEndpoint(),
                'merchantConfig' => VerifyNicAsiaOrder::config(),
            ]);

            if (!isset($response['_embedded'])) {

                // Error in search no transaction result
                $transaction->update([
                    'status' => Payment::PAYMENT_STATUS_PENDING,
                ]);
                flash('error', 'There was error verifying your payment. [Transaction Result]');
                return back();
            }

            $searchResults = $response['_embedded']['transactionSummaries'];
            DB::beginTransaction();

            if (count($searchResults)) {
                foreach ($searchResults as $result) {
                    $transactionDetail = $result;

                    $amountDetail = $transactionDetail['orderInformation']['amountDetails'];
                    if ($transactionDetail['clientReferenceInformation']['code'] != $transaction->transaction_id) {
                        continue;
                    }

                    $orders = optional($transaction->load('orders'))->orders;

                    $amount = PaymentUtil::resolveNicAsiaAmount($transaction, $orders);

                    \Log::error('NIC ERROR', [
                        'amount' => round($amount),
                        'nicAsiaAmt' => round($amountDetail['totalAmount'] ?? 0),
                    ]);

                    //  Total Amount Is Paid
                    if (isset($amountDetail['totalAmount']) && round($amount) <= round($amountDetail['totalAmount'])) {
                        // MArk order as paid
                        $transaction->update([
                            'status' => Payment::PAYMENT_STATUS_DELIVERED,
                        ]);
                        // Create Transaction result here
                        flash('success', 'The payment status is paid for the transaction.');
                        return back();
                    }
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            flash('error', 'There was error verifying your payment.' . $e->getMessage());
            return back();
        }
    }

    private static function config (): array
    {
        return [
            'key'       => NicAsiaConfig::getMerchantKeyID(), // Merchant key ID
            'secretKey' => NicAsiaConfig::getMerchantKeySecret(),// Merchant Secret Key,
            'id'        => NicAsiaConfig::getMerchantId(),
        ];
    }

    private static function requestObjectArr ($refCode): array
    {
        return [
            "save"       => false,
            "name"       => "kunyo",
            "timezone"   => config('app.timezone'),
            "query"      => "clientReferenceInformation.code:".$refCode.' AND submitTimeUtc:[NOW/DAY-7DAYS TO NOW/DAY+1DAY}',
            "offset"     => 0,
            "limit"      => 100,
            "sort"       => "id:asc,submitTimeUtc:asc"
        ];
    }

    /**
     * Generate Signature for cybersource
     *
     * @param string $signatureString
     * @param string $headerString
     * @param array $merchantConfig
     * @return string
     */
    private function generateSignatureTokenHeader($signatureString, $headerString, $merchantConfig): string
    {
        $signatureByteString = utf8_encode($signatureString);
        $decodeKey = base64_decode($merchantConfig['secretKey']);
        $signature = base64_encode(hash_hmac($this->sha256, $signatureByteString, $decodeKey, true));
        $signatureHeader = array(
            'keyid="'.$merchantConfig['key'].'"',
            'algorithm="'.$this->hmacsha256.'"',
            'headers="'.$headerString.'"',
            'signature="'.$signature.'"'
        );
        return $this->signature.implode(", ", $signatureHeader);
    }

    /**
     * Generate digest header
     *
     * @param string $payLoad
     * @return string
     */
    private function generateDigest($payLoad): string
    {
        $utf8EncodedString = utf8_encode($payLoad);
        $digestEncode = hash("sha256", $utf8EncodedString, true);
        return base64_encode($digestEncode);
    }

}
