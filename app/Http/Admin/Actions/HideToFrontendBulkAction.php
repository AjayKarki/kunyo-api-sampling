<?php

namespace App\Http\Controllers\Admin\Actions;

use Illuminate\Http\Request;
use Neputer\Config\Status;

/**
 * Class HideToFrontendBulkAction
 * @package App\Http\Controllers\Admin\Actions
 */
final class HideToFrontendBulkAction
{

    public function __invoke(Request $request)
    {
        app('db')
            ->table('categories')
            ->whereIn('id', explode(',', $request->get('ids')))
            ->update([ 'is_shown' => Status::INACTIVE_STATUS, ]);
        flash('success', 'Records are updated successfully !');
        return redirect()->back();
    }

}
