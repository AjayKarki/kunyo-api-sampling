<?php

namespace App\Http\Controllers\Admin\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Neputer\Supports\BaseController;

/**
 * Class BulkAction
 * @package App\Http\Controllers\Admin\Actions
 */
class DeleteBulkAction extends BaseController
{

    public function __invoke(string $model, Request $request)
    {
        if (Schema::hasTable($model)) {
            app('db')
                ->table($model)
                ->whereIn('id', explode(',', $request->get('ids')))
                ->delete();
            flash('success', 'Records are deleted successfully !');
        } else {
            flash('error', 'Records could not be deleted !');
        }

        return redirect()->back();
    }

}
