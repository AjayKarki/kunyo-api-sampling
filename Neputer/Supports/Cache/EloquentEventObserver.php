<?php

namespace Neputer\Supports\Cache;

use Exception;
use Foundation\Lib\Cache;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class EloquentEventObserver
 * @package Neputer\Supports\Cache
 */
final class EloquentEventObserver
{

    /**
     * This will hold the created/updated/deleted
     * record
     */
    protected $record;

    /**
     * Listen to the Model saved event.
     *
     * @param Model $record
     * @throws Exception
     */
    public function saved(Model $record)
    {
        $this->record = $this->getFullRecord($record);

        $this->assignCache();
    }

    /**
     * Listen to the Model deleted event.
     *
     * @param Model $record
     * @throws Exception
     */
    public function deleted(Model $record)
    {
        $this->record = $record;

        if ($this->record->isIndividualCacheEnabled()) {
            cache()->forget($this->getCacheKey());
        } else {
            $this->setCache($this->removeFromCache($this->record));
        }
    }

    /**
     * Assigns the cache
     *
     * @return bool
     * @throws Exception
     */
    protected function assignCache()
    {
        if ($this->record->isIndividualCacheEnabled()) {
            return $this->setCache($this->record);
        }

        if ($this->record->isCacheAllEnabled()) {

            if (cache($this->getCacheKey())) {
                $records = $this->updateExistingCache($this->record);
            } else {
                $records = $this->record->fetchRecords();
            }
            return $this->setCache($records);
        }
    }

    /**
     * Checks if existing caches has given record, if so it
     * removes and appends the newly created/updated record.
     *
     * @return Collection
     * @throws Exception
     */
    protected function updateExistingCache() {

        return $this->removeFromCache($this->record)->push($this->record);
    }

    /**
     * Collection
     *
     * @return Collection
     * @throws Exception
     */
    protected function removeFromCache() {

        $record = $this->record;

        if (!count(cache($this->getCacheKey()))) {
            $records = $record->fetchRecords();
            return collect($records);
        }

        return collect(cache($this->getCacheKey()))->reject(function($value) use ($record) {
            return $value->{$record->getKeyName()} === $record->{$record->getKeyName()};
        });
    }

    /**
     * Sets the cache.
     *
     * @param $record
     * @return bool
     * @throws Exception
     */
    protected function setCache($record) {

//        $expiresAt = now()->addMinutes(Cache::TIME_INTERVAL);

        return cache()->forever($this->getCacheKey(), $record);
    }

    /**
     * Gets the full record. This query is performed just because when data
     * updated, we won't be getting full record data. So, performing query
     * to get full data.
     *
     * @param   Model  $record  The record
     *
     * @return  Model  The full record.
     */
    protected function getFullRecord($record) {

        $class = get_class($record);

        return $class::find($record->{$record->getKeyName()});
    }

    /**
     * Gets the cache name.
     *
     * @return string
     */
    public function getCacheKey() {
        return ($this->record->isCacheAllEnabled()) ?
            $this->record->getCacheKey() :
            $this->record->getCacheKey().'.'.$this->record->{$this->record->getKeyName()};
    }

}
