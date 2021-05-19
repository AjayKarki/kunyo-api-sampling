<?php

namespace Modules\Application\Http\Services\Home;

use Foundation\Services\BannerService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Application\Http\DTOs\Banner\BannerCollection;

final class HomeService
{

    private BannerService $bannerService;

    public function __construct (
        BannerService $bannerService
    )
    {
        $this->bannerService   = $bannerService;
    }

    public function getBanners ($limit = 4): BannerCollection
    {
        return BannerCollection::fromArray($this->bannerService->byRank($limit)->all());
    }

}
