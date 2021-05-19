<?php
//
//namespace App\Http\Controllers\Admin\Payment;
//
//use Exception;
//use Illuminate\Support\Facades\DB;
//use Illuminate\Support\Facades\Auth;
//
//class NicAsiaController
//{
//
//    /**
//     * Sha 256 digest string
//     *
//     * @var string
//     */
//    protected $sha256digest = 'SHA-256=';
//    protected $hmacsha256 = "HmacSHA256";
//    protected $signature = "Signature:";
//    protected $postalgoheader = "host date (request-target) digest v-c-merchant-id";
//    protected $sha256 = "sha256";
//
//    // Pass reference number and order to verify payment
//    public function verifyOrder($refCode, $order)
//    {
//        $user = Auth::user();
//
//        if (!config('nicasia.debug')) {
//            // Live Environment Keys
//            $merchantConfig = $this->getMerchantConfig('live');
//            $url= config("nicasia.live.verification_url"). "/tss/v2/searches";
//            $host = config("nicasia.live.host");
//        } else {
//            // Test Env
//            $merchantConfig = $this->getMerchantConfig();
//            $url= config("nicasia.test.verification_url"). "/tss/v2/searches";
//            $host = config("nicasia.test.host");
//        }
//
//
//        $requestObjArr = [
//            "save" => false,
//            "name" => "asd",
//            "timezone" => "Asia/Kathmandu",
//            "query" => "clientReferenceInformation.code:".$refCode.' AND submitTimeUtc:[NOW/DAY-7DAYS TO NOW/DAY+1DAY}',
//            "offset" => 0,
//            "limit" => 100,
//            "sort" => "id:asc,submitTimeUtc:asc"
//        ];
//
//        $signatureGeneration = [
//            'host' => $host,
//            'date' => gmdate('D, d M Y H:i:s T'),
//            'methodHeader' => "post",
//            'resourcePath' => "/tss/v2/searches"
//        ];
//        try {
//            /**
//             * Never touch these methods they are taken from docs to generate token
//             * these are magic functions.
//             * take reference from the repo
//             *
//             * https://github.com/CyberSource/cybersource-rest-samples-php
//             *
//             *
//             */
//            $signatureGeneration['digest'] = $this->generateDigest(json_encode($requestObjArr, JSON_PRETTY_PRINT));
//            $signatureString = "host: ".$signatureGeneration['host']."\ndate: ".$signatureGeneration['date']."\n(request-target): ".$signatureGeneration['methodHeader']." ".$signatureGeneration['resourcePath']."\ndigest: ".$this->sha256digest.$signatureGeneration['digest']."\nv-c-merchant-id: ".$merchantConfig['id'];
//            $token = $this->generateSignatureTokenHeader($signatureString, $this->postalgoheader, $merchantConfig);
//            $headers = explode(PHP_EOL, $signatureString);
//            $headers = array_merge($headers, [$token, "Content-Type: application/json"]);
//
//            $curl = curl_init();
//            curl_setopt_array($curl, [
//                CURLOPT_URL => $url,
//                CURLOPT_RETURNTRANSFER => true,
//                CURLOPT_ENCODING => "",
//                CURLOPT_MAXREDIRS => 10,
//                CURLOPT_TIMEOUT => 0,
//                CURLOPT_FOLLOWLOCATION => true,
//                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//                CURLOPT_CUSTOMREQUEST => "POST",
//                CURLOPT_POSTFIELDS => json_encode($requestObjArr, JSON_PRETTY_PRINT),
//                CURLOPT_HTTPHEADER => $headers,
//            ]);
//
//            $response = curl_exec($curl);
//            curl_close($curl);
//
//            $response = json_decode($response, true);
//
//            \Log::error($response);
//
//            if (!isset($response['_embedded'])) {
//
//                // Error in search no transaction result
//                $order->payment_status = 'pending';
//                $order->save();
//                throw new \Exception("There was error verifying your payment.");
//            }
//            $searchResults = $response['_embedded']['transactionSummaries'];
//            DB::beginTransaction();
//
//            if (count($searchResults)) {
//                foreach ($searchResults as $result) {
//                    $transactionDetail = $result;
//
//                    $amountDetail = $transactionDetail['orderInformation']['amountDetails'];
//                    if ($transactionDetail['clientReferenceInformation']['code'] != $order->order_number) {
//                        continue;
//                    }
//                    //  Total Amount Is Paid
//                    if (isset($amountDetail['totalAmount']) && round($order->total) <= round($amountDetail['totalAmount'])) {
//                        // MArk order as paid
//                        $order->payment_status = 'paid';
//                        $order->payment_method_id = $paymentMethod->id;
//                        $order->save();
//                        // Create Transaction result here
//                    }
//                }
//            }
//            DB::commit();
//        } catch (\Exception $e) {
//            DB::rollback();
//            throw new \Exception("There was error verifying your payment.");
//        }
//    }
//
//
//    protected function getMerchantConfig($env = 'test')
//    {
//        return [
//            // Real Keys
//            'key' => config("nicasia.".$env.".merchant_key"),
//            'secretKey' => config("nicasia.".$env.".merchant_secret_key"),
//            'id' => config("nicasia.".$env.".merchant_id")
//        ];
//    }
//
//    /**
//     * Generate Signature for cybersource
//     *
//     * @param string $signatureString
//     * @param string $headerString
//     * @param array $merchantConfig
//     * @return string
//     */
//    protected function generateSignatureTokenHeader($signatureString, $headerString, $merchantConfig)
//    {
//        $signatureByteString = utf8_encode($signatureString);
//        $decodeKey = base64_decode($merchantConfig['secretKey']);
//        $signature = base64_encode(hash_hmac($this->sha256, $signatureByteString, $decodeKey, true));
//        $signatureHeader = array(
//            'keyid="'.$merchantConfig['key'].'"',
//            'algorithm="'.$this->hmacsha256.'"',
//            'headers="'.$headerString.'"',
//            'signature="'.$signature.'"'
//        );
//        return $this->signature.implode(", ", $signatureHeader);
//    }
//
//    /**
//     * Generate digest header
//     *
//     * @param string $payLoad
//     * @return string
//     */
//    protected function generateDigest($payLoad)
//    {
//        $utf8EncodedString = utf8_encode($payLoad);
//        $digestEncode = hash("sha256", $utf8EncodedString, true);
//        return base64_encode($digestEncode);
//    }
//}
