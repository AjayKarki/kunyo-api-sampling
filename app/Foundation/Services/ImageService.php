<?php


namespace Foundation\Services;


use Foundation\Models\Image;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    /**
     * Insert several images into DB
     *
     * @param array $data
     * @param $model
     * @param $path
     * @param $info
     */
    public function insert(array $data, $model, $path, $info = null)
    {
        foreach ($data as $image){
            $url = $image->store($path, 'public');
            $newImage = new Image([
                'path' => 'storage/' . $url ,
                'info' => $info,
            ]);
            $newImage->imageable()->associate($model);
            $newImage->save();
        }
    }

    /**
     * Remove images from database and file
     *
     * @param $data
     * @param string $disk
     */
    public function remove($data, $disk = 'public')
    {
        if($data instanceof Collection){
            $data = $data->pluck('path')->toArray();
        }
        Image::whereIn('path', $data)->delete();
        $data = array_map(function($path) { return str_replace('storage/', '', $path); }, $data);
        Storage::disk($disk)->delete($data);
    }

    /**
     * Get Images of $model Based on Condition
     *
     * @param $model
     * @param array $condition
     * @return Image
     */
    public function getWhere($model, array $condition)
    {
        $query = Image::where('imageable_id', $model->id)->where('imageable_type', $model->getMorphClass());
        if($condition){
            $query->where($condition);
        }
        return $query->get();
    }

}
