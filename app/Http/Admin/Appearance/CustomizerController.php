<?php

namespace App\Http\Controllers\Admin\Appearance;

use Foundation\Lib\HomePage;
use Foundation\Services\SettingService;
use Illuminate\Http\Request;
use Neputer\Supports\BaseController;

/**
 * Class CustomizerController
 * @package App\Http\Controllers\Admin\Appearance
 */
final class CustomizerController extends BaseController
{

    private $settingService;

    public function __construct(
        SettingService $settingService
    )
    {
        $this->settingService = $settingService;
    }

    public function index()
    {
        $data['views'] = HomePage::getViews();
        return view('admin.appearance.customizer.index', compact('data'));
    }

    public function save(Request $request)
    {
        dd($request->all());
    }

}
