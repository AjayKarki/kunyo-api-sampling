<?php

namespace Foundation\Composers;

use Foundation\Lib\Nav;
use Foundation\Lib\Cache;
use Illuminate\View\View;
use Foundation\Services\NavService;

/**
 * Class FooterPrimaryMenuViewComposer
 * @package Foundation\Composers
 */
final class FooterPrimaryMenuViewComposer
{

    /**
     * @var NavService
     */
    private $navService;

    /**
     * PrimaryMenuViewComposer constructor.
     * @param NavService $navService
     */
    public function __construct( NavService $navService )
    {
        $this->navService = $navService;
    }

    /**
     * @param View $view
     */
    public function compose(View $view)
    {
        $view->with('footerPrimaryMenu', $this->navService->bySection(Nav::FOOTER_PRIMARY_MENU));
    }

}
