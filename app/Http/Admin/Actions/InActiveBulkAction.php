<?php

namespace App\Http\Controllers\Admin\Actions;

use Neputer\Config\Status;
use Illuminate\Http\Request;
use Neputer\Supports\BaseController;
use Illuminate\Support\Facades\Schema;

/**
 * Class InActiveBulkAction
 * @package App\Http\Controllers\Admin\Actions
 */
class InActiveBulkAction extends BaseController
{

    public function __invoke(string $model, Request $request)
    {
        if($model !== 'g2a_product_keys') {
            if (Schema::hasTable($model)) {
                app('db')
                    ->table($model)
                    ->whereIn('id', explode(',', $request->get('ids')))
                    ->update(['status' => Status::INACTIVE_STATUS,]);
                flash('success', 'Records are updated successfully !');
            } else {
                flash('error', 'Records could not be updated !');
            }
        } else{
            flash('error', 'Action Not Available for this Model');
        }
        return redirect()->back();
    }

}
