<?php

namespace Modules\DevSupport\Http\Controllers;

use Modules\DevSupport\Lib\DataExtraction;

/**
 * Class InformationAction
 * @package Modules\DevSupport\Http\Controllers
 */
final class InformationAction
{

    public function __invoke()
    {
        $packages = (array) DataExtraction::extractRequiredPackage();
        $server   = (object) DataExtraction::extractServerInfo();
        $app      = (object) DataExtraction::extractAppInfo();
        return view('support::information', compact('packages', 'server', 'app'));
    }

}
