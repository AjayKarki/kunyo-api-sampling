<?php

namespace Modules\Application\Http\Controllers\Collection;

use Foundation\Services\CollectionService;
use Modules\Application\Http\Controllers\BaseController;
use Modules\Application\Http\DTOs\Collection\CollectionData;

final class ViewAction extends BaseController
{

    /**
     * @var CollectionService
     */
    private CollectionService $collectionService;

    public function __construct (CollectionService $collectionService)
    {
        $this->collectionService = $collectionService;
    }

    /**
     * @param $slug
     * @return mixed
     */
    public function __invoke($slug)
    {
        $data = CollectionData::fromModel($this->collectionService->query()->where('slug', $slug)->first());
        return $this->responseOk($data);
    }

}
