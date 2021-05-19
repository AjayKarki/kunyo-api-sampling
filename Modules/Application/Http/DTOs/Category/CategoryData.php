<?php

namespace Modules\Application\Http\DTOs\Category;

use Foundation\Models\Category;
use Spatie\DataTransferObject\DataTransferObject;

final class CategoryData extends DataTransferObject
{

    public string $category_name;

    public string $slug;

    public ?string $image;

    public ?string $seo_title;

    public ?string $seo_description;

    public static function fromModel(Category $category): DataTransferObject
    {
        return new self([
            'category_name'     => $category->category_name,
            'slug'              => $category->slug,
            'image'             => get_image_url('category', $category->image),
            'seo_title'         => $category->seo_title,
            'seo_description'   => $category->seo_description,
        ]);
    }

}
