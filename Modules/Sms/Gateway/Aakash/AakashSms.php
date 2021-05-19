<?php

namespace Modules\Sms\Gateway\Aakash;

use Foundation\Lib\Meta;
use Illuminate\Support\Arr;
use Illuminate\Http\Response;
use Foundation\Services\SettingService;
use Modules\Sms\Template;
use Modules\Sms\TemplateConfig;

/**
 * Class AakashSms
 * @package Modules\Sms\Gateway\Sparrow
 */
final class AakashSms
{

    const SMS_KEY = 'sms';

    /**
     * The Sms foundation class instance
     *
     * @var $sms
     */
    private $sms;

    /**
     * The SiteSetting Instance
     *
     * @var $settingService
     */
    private $settingService;

    /**
     * The Template Instance
     *
     * @var $template
     */
    private $template;

    /**
     * The sms option settings
     *
     * @var $smsOptions
     */
    private $smsOptions;

    /**
     * Resolved message for the sms
     *
     * @var $message
     */
    private $message;

    /**
     * SmsHandler constructor.
     *
     * @param Sms $sms
     * @param SettingService $settingService
     * @param Template $template
     */
    public function __construct(
        Sms $sms,
        SettingService $settingService,
        Template $template
    )
    {
        $this->sms = $sms;
        $this->settingService = $settingService;
        $this->template = $template;
    }

    /**
     * Set the resolved message and return $this
     *
     * @param string $template
     * @param array $placeholders
     * @return $this
     */
    public function resolveMessage(string $template, array $placeholders = [])
    {
        $content = TemplateConfig::getTemplateContent($template);
        $placeholders = array_merge($placeholders, [
//            '{ORDER_ID}'   => 'TESTING',
        ]);

        return $this->setMessage($content, $placeholders, $template);
    }

    /**
     * Set Custom Message (without using template)
     *
     * @param $content
     * @param $placeholders
     * @param string $template
     * @return $this
     */
    public function setMessage($content, $placeholders, $template = '')
    {
        $this->message = $this->template->handle($template, $content, $placeholders);
        return $this;
    }

//    public function setMessage($content)
//    {
//        $this->message = $content;
//        return $this;
//    }

    /**
     * @param $to
     * @return array
     */
    public function handle( $to )
    {
        if (AakashConfig::getStatus()) {
            $status = $this->sms->send([
                'auth_token' => AakashConfig::getToken(),
                'to'    => $to,
                'text'  => $this->message,
            ]);

            if (Arr::get((array) $status, 'response.error')) {
                \Log::debug('AakashSMS failed : '. $to, array_merge((array) $status, [
                    'need_to_send_message' => $this->message,
                ]));
            }

            Meta::set(
                AakashConfig::dbKey(),
                Arr::get((array) $status, 'response.credit_available') ?? 0
            );

            return $status;
        }

        \Log::debug('AakashSMS failed : '. $to, [
            'reason'  => 'Sms gateway is disabled !',
            'need_to_send_message' => $this->message,
        ]);

        return [
            'status'   => Response::HTTP_INTERNAL_SERVER_ERROR,
            'response' => 'Sms gateway is disabled !',
        ];
    }

}
