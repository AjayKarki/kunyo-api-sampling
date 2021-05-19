<?php

namespace App\Http\Controllers\Admin\Actions;

use Illuminate\Http\Request;
use Modules\Application\Libs\Api;
use Neputer\Supports\Mixins\Responsable;
use Foundation\Services\CategoryService;
use Foundation\Services\CollectionService;
use Foundation\Lib\Category as CategoryType;

final class GetCategories
{

    use Responsable;

    /**
     * @var CategoryService
     */
    private CategoryService $categoryService;

    /**
     * @var CollectionService
     */
    private CollectionService $collectionService;

    public function __construct (
        CategoryService $categoryService,
        CollectionService $collectionService
    )
    {
        $this->categoryService = $categoryService;
        $this->collectionService = $collectionService;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function __invoke(Request $request)
    {
        $data = [];

        $term = $request->input('term.term');
        $type = $request->input('type');

        if ($type == Api::HOME_API_CONTENT_TYPE_COLLECTION) {
            $data = $this->collectionService->searchByTerm($term);
        } else if ($type == 4) {
            $data = $this->categoryService->searchProductCategories($term);
        }
        else {
            $data = $this->categoryService->search(
                $type ? CategoryType::TYPE_GIFT_CARD_CATEGORY : CategoryType::TYPE_IN_GAME_TOP_UP_CATEGORY, $term);
        }

        return $this->responseOk($data);
    }

}
