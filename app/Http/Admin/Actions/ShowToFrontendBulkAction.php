<?php

namespace App\Http\Controllers\Admin\Actions;

use Illuminate\Http\Request;
use Neputer\Config\Status;

/**
 * Class ShowToFrontendBulkAction
 * @package App\Http\Controllers\Admin\Actions
 */
final class ShowToFrontendBulkAction
{

    public function __invoke(Request $request)
    {
        app('db')
            ->table('categories')
            ->whereIn('id', explode(',', $request->get('ids')))
            ->update([ 'is_shown' => Status::ACTIVE_STATUS, ]);
        flash('success', 'Records are updated successfully !');
        return redirect()->back();
    }

}
