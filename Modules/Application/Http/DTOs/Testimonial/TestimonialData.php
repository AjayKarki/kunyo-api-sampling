<?php

namespace Modules\Application\Http\DTOs\Testimonial;

use Neputer\Supports\Utility;
use Foundation\Models\Testimonial;
use Spatie\DataTransferObject\DataTransferObject;

final class TestimonialData extends DataTransferObject
{

    public string $key;

    public int $rating;

    public string $name;

    public string $email;

    public string $designations;

    public string $description;

    public static function fromModel(Testimonial $testimonial): DataTransferObject
    {
        return new self([
            'key'           => \Str::slug($testimonial->name) . Utility::generateRandomNumber(),
            'rating'        => (int) $testimonial->rating,
            'name'          => $testimonial->name,
            'email'         => $testimonial->email,
            'designations'  => $testimonial->designations,
            'description'   => $testimonial->description,
        ]);
    }

}
