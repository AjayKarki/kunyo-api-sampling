<?php

namespace Foundation\Mixins;

use Illuminate\Http\Request;

/**
 * Trait HasEssentials
 * @package Foundation\Mixins
 */
trait HasEssentials
{

    private function requestHelper(Request $request)
    {
        if (!empty((string) $request->get('platform_id'))) {
            $request->merge([
                'platform_id' => $this->getInsertedId($request->get('platform_id'), 'platforms'),
            ]);
        }

        if (!empty((string) $request->get('publisher_id'))) {
            $request->merge([
                'publisher_id' => $this->getInsertedId($request->get('publisher_id'), 'publishers'),
            ]);
        }

        if (!empty((string) $request->get('developer_id'))) {
            $request->merge([
                'developer_id' => $this->getInsertedId($request->get('developer_id'), 'developers'),
            ]);
        }

        if (!empty((string) $request->get('genre_id'))) {
            $request->merge([
                'genre_id' => $this->getInsertedId($request->get('genre_id'), 'genres'),
            ]);
        }

        if (!empty((string) $request->get('delivery_mode_id'))) {
            $request->merge([
                'delivery_mode_id' => $this->getInsertedId($request->get('delivery_mode_id'), 'delivery_modes'),
            ]);
        }

        if (!empty((string) $request->get('delivery_time_id'))) {
            $request->merge([
                'delivery_time_id' => $this->getInsertedId($request->get('delivery_time_id'), 'delivery_times'),
            ]);
        }

        if (!empty((string) $request->get('region_id'))) {
            $request->merge([
                'region_id' => $this->getInsertedId($request->get('region_id'), 'regions'),
            ]);
        }

        return $request;
    }

    private function getInsertedId($id, $tableName)
    {
        return
            app('db')
                ->table($tableName)
                ->where('id', $id)->value('id') ?? app('db')
                ->table($tableName)
                ->insertGetId([
                    'name' => $id,
                    'created_at' => now(),
                ]);
    }

}
