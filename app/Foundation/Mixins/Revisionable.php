<?php

namespace Foundation\Mixins;

use Foundation\Models\Revision;

/**
 * Trait Revisionable
 * @package Foundation\Mixins
 */
trait Revisionable
{

    /**
     * Get all of the revisions for the entity/model.
     */
    public function revisions()
    {
        return $this->morphMany(Revision::class, 'revisionable');
    }

}
