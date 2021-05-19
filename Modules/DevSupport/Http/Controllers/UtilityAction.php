<?php

namespace Modules\DevSupport\Http\Controllers;

use Modules\DevSupport\Lib\DataExtraction;

/**
 * Class UtilityAction
 * @package Modules\DevSupport\Http\Controllers
 */
final class UtilityAction
{

    public function __invoke()
    {
        return view('support::utility');
    }

}
