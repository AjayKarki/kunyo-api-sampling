<?php

namespace Modules\Application\Http\DTOs\Testimonial;

use Foundation\Models\Testimonial;
use Spatie\DataTransferObject\DataTransferObjectCollection;

final class TestimonialCollection extends DataTransferObjectCollection
{

    public function current(): TestimonialData
    {
        return parent::current();
    }

    public static function fromArray(array $data): TestimonialCollection
    {
        return new TestimonialCollection(
            array_map(fn (Testimonial $item) => TestimonialData::fromModel($item), $data)
        );
    }

}
