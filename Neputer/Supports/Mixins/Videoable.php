<?php

namespace Neputer\Supports\Mixins;

use Foundation\Models\Video;

/**
 * Trait Videoable
 *
 * @package Neputer\Supports\Mixins
 */
trait Videoable
{

    public function videos()
    {
        return $this->morphMany(Video::class, 'videoable');
    }

}
