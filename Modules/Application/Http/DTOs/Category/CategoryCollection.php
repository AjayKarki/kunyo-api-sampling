<?php

namespace Modules\Application\Http\DTOs\Category;

use Foundation\Models\Category;
use Spatie\DataTransferObject\DataTransferObjectCollection;

final class CategoryCollection extends DataTransferObjectCollection
{

    public function current(): CategoryData
    {
        return parent::current();
    }

    public static function fromArray(array $data): CategoryCollection
    {
        return new CategoryCollection(
            array_map(fn (Category $item) => CategoryData::fromModel($item), $data)
        );
    }

}
