<?php

namespace App\Http\Controllers\Admin\Actions;

use Illuminate\Http\Request;
use Neputer\Supports\Mixins\Responsable;

/**
 * Class SearchGiftCard
 * @package App\Http\Controllers\Admin\Actions
 */
final class SearchGiftCard
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
                ->table('gift_cards')
                ->select('name', 'id')
                ->where('name', 'like', '%' . $term . '%')
                ->limit(10)
                ->get() ;
        }

        return $this->responseOk($data);
    }

}
