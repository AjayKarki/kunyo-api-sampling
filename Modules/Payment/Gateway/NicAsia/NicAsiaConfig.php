<?php

namespace Modules\Payment\Gateway\NicAsia;

use Neputer\Supports\ConfigInstance;
use Neputer\Supports\Utility;

final class NicAsiaConfig extends ConfigInstance
{

    const CONFIG_KEY = 'gateway';

    const UNDEFINED = 'N/A';

    const CURRENCY = [
        'NPR'      => 'NPR',
        'USD'      => 'USD',
    ];

    const TRANSACTION_TYPE = 'sale';

    const LOCALE = 'en';

    const CARD_TYPE = [
        '001'       => 'Visa',
        '002'       => 'Mastercard',
        '003'       => 'American Express',
        '004'       => 'Discover',
        '005'       => 'Diners Club', // Cards starting with 54 or 55 or rejected
        '006'       => 'Carte Blanche',
        '007'       => 'JSB',
        '014'       => 'EnRoute',
        '021'       => 'JAL',
        '024'       => 'Maestro UK Domestic',
        '031'       => 'Delta',
        '033'       => 'Visa Electron',
        '034'       => 'Dankort',
        '036'       => 'Carte Bancaire',
        '037'       => 'Carta Si',
        '042'       => 'Maestro International',
        '043'       => 'GE Money UK card',
        '050'       => 'Hipecard (sale only)',
    ];

    const REQUIRED_SIGNED_FIELDS = [
        'access_key',
        'amount',
        'currency',
        'locale',
        'profile_id',
        'reference_number',
        'signed_date_time',
        'signed_field_names',
        'transaction_type',
        'transaction_uuid',
        'unsigned_field_names',
        // Optional fields but mandatory for the Kunyo.co
        'bill_to_email',
        'bill_to_forename',
        'bill_to_surname',
        'bill_to_phone',

        "bill_to_address_city",
        "bill_to_address_country",
        "bill_to_address_line1",
        "bill_to_address_postal_code",
        "bill_to_address_state",
        "bill_to_email",
        "bill_to_forename",
        "bill_to_phone",
        "bill_to_surname",
    ];

    const UN_REQUIRED_FIELDS = [
        'bill_to_address_city',
        'bill_to_address_country',
        'bill_to_address_line1',
        'bill_to_address_postal_code',
        'bill_to_address_state',
        'auth_trans_ref_no',
        'payment_method',
    ];

    const REQUIRED_UNSIGNED_FIELDS = [
        'card_type', 'card_number', 'card_expiry_date',
    ];

    const VERIFICATION_DATA = [
        'transaction_id',
        'decision',
        'req_access_key',
        'req_profile_id',
        'req_transaction_uuid',
        'req_transaction_type',
        'req_reference_number',
        'req_amount',
        'req_currency',
        'req_locale',
        'req_payment_method',
        'req_bill_to_forename',
        'req_bill_to_surname',
        'req_bill_to_email',
        'req_bill_to_phone',
        'req_bill_to_address_line1',
        'req_bill_to_address_city',
        'req_bill_to_address_state',
        'req_bill_to_address_country',
        'req_bill_to_address_postal_code',
        'req_card_number',
        'req_card_type',
        'req_card_type_selection_indicator',
        'req_card_expiry_date',
        'card_type_name',
        'message',
        'reason_code',
        'auth_avs_code',
        'auth_response',
        'auth_amount',
        'auth_code',
        'auth_trans_ref_no',
        'auth_time',
        'request_token',
        'bill_trans_ref_no',
        'payer_authentication_proof_xml',
        'payer_authentication_reason_code',
        'payer_authentication_enroll_e_commerce_indicator',
        'payer_authentication_enroll_veres_enrolled',
        'signed_field_names',
        'signed_date_time',
    ];

    public static function isEnabled()
    {
        return NicAsiaConfig::pull(self::CONFIG_KEY, 'nicasia.is_enabled');
    }

    public static function getEndpoint()
    {
        return NicAsiaConfig::pull(self::CONFIG_KEY, 'nicasia.endpoint');
    }

    public static function getHost()
    {
        return NicAsiaConfig::pull(self::CONFIG_KEY, 'nicasia.verify_endpoint');
    }

    public static function getVerifyEndpoint()
    {
        $endpoint = NicAsiaConfig::pull(self::CONFIG_KEY, 'nicasia.verify_endpoint');
        return $endpoint . (Utility::endOfStrIs($endpoint, '/') ? 'tss/v2/searches' : '/tss/v2/searches');
    }

    public static function getMerchantId() // Profile ID
    {
        return NicAsiaConfig::pull(self::CONFIG_KEY, 'nicasia.merchant_id');
    }

    public static function secretKey()
    {
        return NicAsiaConfig::pull(self::CONFIG_KEY, 'nicasia.secret_key');
    }

    public static function accessKey()
    {
        return NicAsiaConfig::pull(self::CONFIG_KEY, 'nicasia.access_key');
    }

    public static function profileId()
    {
        return NicAsiaConfig::pull(self::CONFIG_KEY, 'nicasia.profile_id');
    }

    public static function currency()
    {
        return NicAsiaConfig::pull(self::CONFIG_KEY, 'nicasia.currency');
    }

    public static function paymentMethod()
    {
        return NicAsiaConfig::pull(self::CONFIG_KEY, 'nicasia.payment_method');
    }

    public static function cardType()
    {
        return NicAsiaConfig::pull(self::CONFIG_KEY, 'nicasia.card_type');
    }

    public static function getMerchantKeyID()
    {
        return NicAsiaConfig::pull(self::CONFIG_KEY, 'nicasia.merchant_key_id');
    }

    public static function getMerchantKeySecret()
    {
        return NicAsiaConfig::pull(self::CONFIG_KEY, 'nicasia.merchant_key_secret');
    }

}
