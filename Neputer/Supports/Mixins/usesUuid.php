<?php

namespace Neputer\Supports\Mixins;

use Illuminate\Support\Str;

/**
 * Trait usesUuid
 * @package Neputer\Supports\Mixins
 */
trait usesUuid
{

    protected static function bootUsesUuid()
    {
        static::creating(function ($model) {
            if (! $model->getKey()) {
                $model->{$model->getUuidKey()} = (string) hexdec(uniqid());
            }
        });
    }

    abstract public function getUuidKey();

}
