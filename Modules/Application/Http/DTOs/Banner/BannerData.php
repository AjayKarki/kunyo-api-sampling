<?php

declare(strict_types=1);

namespace Modules\Application\Http\DTOs\Banner;

use Foundation\Models\Banner;
use Spatie\DataTransferObject\DataTransferObject;

final class BannerData extends DataTransferObject
{

    public string $name;

    public ?string $caption;

    public ?string $image;

    public ?string $url;

    public ?string $open_in;

    public static function fromModel(Banner $banner): DataTransferObject
    {
        return new self([
            'name'      => $banner->name,
            'caption'   => $banner->caption,
            'image'     => get_image_url('banner', $banner->image),
            'url'       => $banner->url,
            'open_in'   => $banner->open_in,
        ]);
    }

}
