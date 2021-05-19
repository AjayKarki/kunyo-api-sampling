<?php

namespace Modules\Sms;

use Neputer\Supports\Utility;

/**
 * Class Template
 * @package Modules\Sms
 */
final class Template
{

    /**
     * Patterns for template : order_is_created
     *
     * @var array
     */
    public static $orderIsCreated = [
        '{ORDER_ID}',
    ];

    /**
     * Patterns for template : order_is_received
     *
     * @var array
     */
    public static $orderIsReceived = [
        '{RECEIVER_NAME}',
        '{ORDER_ID}',
    ];

    /**
     * Patterns for template : send_otp_code
     *
     * @var array
     */
    public static $sendOtpCode = [
        '{OTP_CODE}',
    ];

    public static $phoneIsVerified = [];

    /**
     * Patterns for template : order_is_redeemed
     *
     * @var array
     */
    public static $orderIsRedeemed = [
        '{RECEIVER_NAME}',
        '{ORDER_ID}',
    ];

    /**
     * Return the resolved content ie. if patterns/placeholders exists replaced with given replacements
     *
     * @param string $template_name
     * @param string $template_content
     * @param array $placeholders
     * @return mixed
     */
    public function handle(string $template_name, string $template_content, array $placeholders)
    {
        return Utility::resolvePatterns($template_content, $placeholders);
    }

    /**
     * Return resolved patterns according to given template name
     *
     * @param string $template_name
     * @return array
     */
    private function resolvedPatterns(string $template_name)
    {
        $patterns = [];
        switch ($template_name) {
            case "order_is_created":
                $patterns = static::$orderIsCreated;
                break;
            case "order_is_received":
                $patterns = static::$orderIsReceived;
                break;
        }
        return $patterns;
    }

}
