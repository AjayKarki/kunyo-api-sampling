<?php

namespace Modules\Payment\Gateway\NicAsia;

use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;

final class NicAsiaHandler
{

    public static function generateSignature($payload = []): string
    {
        $fields = [];

        $signedFieldKeys = $payload['signed_field_names'];

        $signedFields = explode(',', $signedFieldKeys);

        foreach ($signedFields as $key => $item) {
            $fields[] = sprintf("%s=%s", $item, $payload[$item]);
        }

        $fieldsStr = implode(',', $fields);

        return base64_encode(hash_hmac('sha256', $fieldsStr, NicAsiaConfig::secretKey(), true));
    }

    public static function handle($data): array
    {
        $transactionId = Arr::get($data, 'transaction_id');
        $amount = Arr::get($data, 'amount');

        $importantArgs = [
            'amount'            => $amount,
            'reference_number'  => $transactionId,
            'transaction_uuid'  => $transactionId,
            'auth_trans_ref_no' => $transactionId,
        ];

        return NicAsiaHandler::resolvePayload($importantArgs);
    }

    public static function resolveSignatureKeys (Request $request) : array
    {
        return $request->only(
            explode(',', $request->get('signed_field_names'))
        );
    }

    private static function resolveSignaturePayload($data) : array
    {
        \Log::info('Signed Field', NicAsiaConfig::REQUIRED_SIGNED_FIELDS);
        return [
            "access_key"                        => NicAsiaConfig::accessKey(),
            "amount"                            => Arr::get($data, 'amount'),
            "bill_to_address_city"              => 'Kathmandu',
            "bill_to_address_country"           => 'NP',
            "bill_to_address_line1"             => 'Kathmandu',
            "bill_to_address_postal_code"       => 'Kathmandu',
            "bill_to_address_state"             => 'Kathmandu',
            "bill_to_email"                     => optional(auth()->user())->email,
            "bill_to_forename"                  => optional(auth()->user())->first_name,
            "bill_to_phone"                     => optional(auth()->user())->phone_number,
            "bill_to_surname"                   => optional(auth()->user())->last_name,
            "currency"                          => NicAsiaConfig::currency(), // For live use 'NPR'; For test use 'USD'
            "locale"                            => NicAsiaConfig::LOCALE,
            "payment_method"                    => NicAsiaConfig::paymentMethod(),
            "profile_id"                        => NicAsiaConfig::profileId(),
            "reference_number"                  => Arr::get($data, 'reference_number'),
            "signed_date_time"                  => gmdate("Y-m-d\TH:i:s\Z"),
            "signed_field_names"                => implode(',', NicAsiaConfig::REQUIRED_SIGNED_FIELDS),
            "transaction_type"                  => NicAsiaConfig::TRANSACTION_TYPE,
            "transaction_uuid"                  => Arr::get($data, 'transaction_uuid'),
            "unsigned_field_names"              => implode(',', NicAsiaConfig::REQUIRED_UNSIGNED_FIELDS),
            "auth_trans_ref_no"                 => Arr::get($data, 'auth_trans_ref_no'),
        ];
    }

    private static function resolvePayload($data) : array
    {
        return array_merge(NicAsiaHandler::resolveSignaturePayload($data), [
            'card_type'        => NicAsiaConfig::cardType(),
            'card_number'      => '',
            'card_expiry_date' => '',
            'signature'        => NicAsiaHandler::generateSignature(
                NicAsiaHandler::resolveSignaturePayload($data)
            ),
        ]);
    }

    public static function isSignatureValid($oldSignature, $payload): bool
    {
        $newSignature = NicAsiaHandler::generateSignature(
            $payload
        );

        return strcmp($newSignature, $oldSignature) == 0;
    }

}
