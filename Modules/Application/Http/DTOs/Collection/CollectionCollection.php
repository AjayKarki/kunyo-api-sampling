<?php

namespace Modules\Application\Http\DTOs\Collection;

use Foundation\Models\Collection;
use Spatie\DataTransferObject\DataTransferObjectCollection;

final class CollectionCollection extends DataTransferObjectCollection
{

    public function current(): CollectionData
    {
        return parent::current();
    }

    public static function fromArray(array $data): CollectionCollection
    {
        return new CollectionCollection(
            array_map(fn (Collection $item) => CollectionData::fromModel($item), $data)
        );
    }

}
