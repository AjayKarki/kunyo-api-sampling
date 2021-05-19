<?php

namespace Foundation\Composers;

use Foundation\Lib\Nav;
use Foundation\Lib\Cache;
use Illuminate\View\View;
use Foundation\Services\NavService;

/**
 * Class PrimaryMenuViewComposer
 * @package Foundation\Composers
 */
final class PrimaryMenuViewComposer
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
        $view->with('primaryMenu', $this->navService->bySection(Nav::PRIMARY_MENU));
    }

}
