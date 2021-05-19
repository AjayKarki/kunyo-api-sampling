<?php

namespace Modules\Application\Http\DTOs\Banner;

use Foundation\Models\Banner;
use Spatie\DataTransferObject\DataTransferObjectCollection;

final class BannerCollection extends DataTransferObjectCollection
{

    public function current(): BannerData
    {
        return parent::current();
    }

    public static function fromArray(array $data): BannerCollection
    {
        return new BannerCollection(
            array_map(fn (Banner $item) => BannerData::fromModel($item), $data)
        );
    }

}
