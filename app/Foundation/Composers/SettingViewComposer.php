<?php

namespace Foundation\Composers;

use Foundation\Lib\Nav;
use Illuminate\View\View;
use Illuminate\Support\Arr;
use Foundation\Services\SettingService;

/**
 * Class SettingViewComposer
 * @package Foundation\Composers
 */
final class SettingViewComposer
{

    private $settingService;

    /**
     * SettingViewComposer constructor.
     * @param SettingService $settingService
     */
    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    /**
     * @param View $view
     */
    public function compose(View $view)
    {
        $view->with('settings', Arr::except($this->settingService->getSettings(), [ 'payment', 'sms', ]));
    }

}
