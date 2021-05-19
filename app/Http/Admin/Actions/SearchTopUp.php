<?php

namespace App\Http\Controllers\Admin\Actions;

use Illuminate\Http\Request;
use Neputer\Supports\Mixins\Responsable;

final class SearchTopUp
{

    use Responsable;

    /**
     * @param Request $request
     * @return mixed
     */
    public function __invoke(Request $request)
    {
        $data = [];

        if ($term = $request->input('term.term')) {
            $data = app('db')
                ->table('game_top_ups')
                ->select('name', 'id')
                ->where('name', 'like', '%' . $term . '%')
                ->limit(10)
                ->get() ;
        }

        return $this->responseOk($data);
    }

}
