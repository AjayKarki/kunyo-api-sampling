<?php

namespace Modules\Application\Http\Controllers\Collection;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Foundation\Services\CategoryService;
use Foundation\Services\CollectionService;
use Foundation\Lib\Category as CategoryType;
use Neputer\Supports\DTO\ResponsePaginationData;
use Modules\Application\Http\Controllers\BaseController;
use Modules\Application\Http\DTOs\Category\CategoryCollection;

final class ListAction extends BaseController
{

    /**
     * @var CollectionService
     */
    private CollectionService $collectionService;
    /**
     * @var CategoryService
     */
    private CategoryService $categoryService;

    public function __construct(CollectionService $collectionService, CategoryService $categoryService)
    {
        $this->collectionService = $collectionService;
        $this->categoryService = $categoryService;
    }

    public function __invoke($slug, Request $request) : ResponsePaginationData
    {
        $collectionId = $this->collectionService->query()->where('slug', $slug)->value('id');
        $categories = $this->categoryService->byCollection($collectionId, 20);
        $collection = CategoryCollection::fromArray($categories->items());
        return new ResponsePaginationData([
            'paginator'  => $categories,
            'collection' => $collection,
            'total'      => $this->categoryService->total($collectionId),
            'limit'      => 20,
        ]);
    }

}
