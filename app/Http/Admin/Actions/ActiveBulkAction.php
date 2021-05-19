<?php

namespace App\Http\Controllers\Admin\Actions;

use Neputer\Config\Status;
use Illuminate\Http\Request;
use Neputer\Supports\BaseController;
use Illuminate\Support\Facades\Schema;

/**
 * Class ActiveBulkAction
 * @package App\Http\Controllers\Admin\Actions
 */
class ActiveBulkAction extends BaseController
{

    public function __invoke(string $model, Request $request)
    {
        if($model !== 'g2a_product_keys'){
            if (Schema::hasTable($model)) {
                app('db')
                    ->table($model)
                    ->whereIn('id', explode(',', $request->get('ids')))
                    ->update([ 'status' => Status::ACTIVE_STATUS, ]);
                flash('success', 'Records are active successfully !');
            } else {
                flash('error', 'Records could not be active !');
            }
        } else{
            flash('error', 'Action Not Available for this Model');
        }
        return redirect()->back();
    }

}
