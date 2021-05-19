<?php

namespace Foundation\Lib;

/**
 * Class PostType
 * @package App\Foundation\Lib
 */
final class PostType
{

    /**
     * Post Type Post
     */
    const POST_TYPE_POST = 0;

    /**
     * Post Type Page
     */
    const POST_TYPE_PAGE = 1;

    /**
     * FAQ Support
     */
    const POST_TYPE_SUPPORT = 2;

    /**
     * @var $current
     */
    public static $current = [
        self::POST_TYPE_POST    => 'post',
        self::POST_TYPE_PAGE    => 'page',
        self::POST_TYPE_SUPPORT => 'support',
    ];

    const PAGE_PRIVACY_POLICY   = 'privacy-and-policy';
    const PAGE_TERMS_CONDITIONS = 'terms-and-conditions';
    const PAGE_CONTACT_US       = 'contact-us';
    const PAGE_ABOUT_US         = 'about-us';

    public static function pages(): array
    {
        return [
            PostType::PAGE_PRIVACY_POLICY    => 'Privacy and policy',
            PostType::PAGE_TERMS_CONDITIONS  => 'Terms and conditions',
            PostType::PAGE_CONTACT_US        => 'Contact us',
            PostType::PAGE_ABOUT_US          => 'About Us',
        ];
    }
}
