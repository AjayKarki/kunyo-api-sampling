<?php

namespace App\Http\Controllers\Admin\Actions;

use Neputer\Config\Status;
use Illuminate\Http\Request;
use Neputer\Supports\BaseController;
use Illuminate\Support\Facades\Schema;

class StatusAction extends BaseController
{

    public function __invoke(string $model, Request $request)
    {
        if(Schema::hasTable($model)){
            return $this->responseOk(
                app('db')
                    ->table($model)
                    ->where('id', $request->get('id'))
                    ->update([
                        'status' => $request->get('status') === Status::$current[Status::ACTIVE_STATUS] ? Status::INACTIVE_STATUS : Status::ACTIVE_STATUS,
                    ])
            );
        }
    }
}
