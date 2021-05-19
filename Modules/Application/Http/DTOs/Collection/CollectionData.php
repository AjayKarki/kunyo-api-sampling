<?php

namespace Modules\Application\Http\DTOs\Collection;

use Foundation\Models\Collection;
use Spatie\DataTransferObject\DataTransferObject;

final class CollectionData extends DataTransferObject
{

    public string $name;

    public string $slug;

    public static function fromModel(Collection $collection): DataTransferObject
    {
        return new self([
            'name'     => $collection->name,
            'slug'     => $collection->slug,
        ]);
    }

}
