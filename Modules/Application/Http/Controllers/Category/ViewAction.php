<?php

namespace Modules\Application\Http\Controllers\Category;

use Illuminate\Http\JsonResponse;
use Foundation\Services\CategoryService;
use Modules\Application\Http\Controllers\BaseController;
use Modules\Application\Http\DTOs\Category\CategoryData;
use Modules\Application\Http\DTOs\Category\CategoryCollection;

/**
 * Class ViewAction
 * @package Modules\Application\Http\Controllers\Category
 */
final class ViewAction extends BaseController
{

    private CategoryService $categoryService;

    public function __construct( CategoryService $categoryService )
    {
        $this->categoryService = $categoryService;
    }

    public function __invoke($id) : JsonResponse
    {
        $data = CategoryData::fromModel($this->categoryService->query()->find($id));
        $data->children = CategoryCollection::fromArray($this->categoryService->getRespectiveChildCategory($id)->all())->items();
        return $this->responseOk(
            $data
        );
    }

}
