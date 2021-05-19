<?php

namespace Modules\Email;

use Foundation\Services\EmailTemplateService;

/**
 * Class EmailHandler
 * @package Modules\Email
 */
final class EmailHandler
{

    /**
     * @var EmailTemplateService
     */
    private $template;

    /**
     * Add the pattern where you want to add button
     *
     * @var array
     */
    private $buttons = [
        '{USER_VERIFICATION_LINK}',
        '{PASSWORD_RESET_TOKEN_LINK}',
        '{TICKET_LINK}',
    ];

    /**
     * Handler constructor.
     * @param EmailTemplateService $template
     */
    public function __construct( EmailTemplateService $template )
    {
        $this->template = $template;
    }

    /**
     * Returns the content
     *
     * @param string $slug
     * @param array $params
     * @param array $extras
     * @param array $options
     * @return array
     */
    public function handle ( string $slug, array $params = [], array $extras = [], $options = [])
    {
        return self::resolveContent($slug, $params, $extras, $options);
    }

    /**
     * Returns the resolved content and extra key information
     *
     * @param $slug
     * @param array $params
     * @param array $extras
     * @param array $options
     * @return array
     */
    private function resolveContent ( $slug, array $params, array $extras, array $options)
    {
        if(array_key_exists('custom-template', $options))
            $template = (object) ['body' => $options['custom-template']];
        else
            $template = optional($this->template->findBySlug($slug))->body;


        $patterns     = [];
        $replacements = [];

        foreach ($params as $key => $value) {
            $patterns[]     = $key;
            $replacements[] = $this->resolveElementType($key , $value);
        }

        return [
            'content'       => str_replace([ '{', '}', ], '', preg_replace($patterns, $replacements, $template)),
            'receiver'      => $extras['receiver'] ?? '',
            'sender'        => $extras['sender'] ?? '',
            'url'           => $extras['url'] ?? config('app.url'),
            'user_initials' => $extras['user_initials'] ?? 'N/A',
        ];
    }

    /**
     * Resolving the element type
     *
     * @param $key
     * @param $value
     * @return string
     */
    private function resolveElementType($key , $value)
    {
        return $this->resolveButton($key , $value);
    }

    /**
     * If it's a button return the html / the value itself
     *
     * @param $key
     * @param $value
     * @return string
     */
    private function resolveButton( $key, $value )
    {
        if ( in_array( $key, $this->buttons ) ) {
            return "<a href='{$value}' class='button button-blue' target='_blank'>Click Here</a>";
        }
        return $value;
    }

}
