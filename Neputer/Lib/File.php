<?php

namespace Neputer\Lib;

use File as BaseFile;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Filesystem\Filesystem;

/**
 * Class File
 * @package Neputer\Lib
 */
final class File
{

    const IMAGE_MIME_TYPES = [
        'image/gif',
        'image/jpeg',
        'image/jpg',
        'image/png'
    ];

    const DEFAULT_DIMENSIONS = [

    ];

    /**
     * Return storage disk
     *
     * @param string $disk
     * @return Filesystem
     */
    public static function storage(string $disk = 'public')
    {
        return Storage::disk($disk);
    }

    /**
     * Upload file
     *
     * @param $file
     * @param string $folder
     * @param bool $thumbs
     * @param null $existing
     * @return string
     */
    public static function upload($file, string $folder, bool $thumbs = true, $existing = null)
    {
        $name = $file->getClientOriginalName();
        $imageName = Str::random(24) .'_'.$name;
        $file_path = 'images'.DIRECTORY_SEPARATOR.$folder.DIRECTORY_SEPARATOR.$imageName;
        self::storage()->put($file_path,  BaseFile::get($file));

        if ($thumbs) {
            self::uploadThumbs($file, $folder, $imageName);
        }

        // $storagePath = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix();
        if($existing) {
            self::delete('images'.DIRECTORY_SEPARATOR.$folder.DIRECTORY_SEPARATOR.$existing);
            self::deleteThumbs();
        }
        return $imageName;
    }

    public static function uploadThumbs($file, $folder, $imageName)
    {
        $image_dimension = self::DEFAULT_DIMENSIONS;

        foreach ($image_dimension as $dimension) {
            $image = Image::make($file)->resize($dimension['w'], $dimension['h']);
            self::storage()
                ->put('images'.DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $dimension['w'] . '_' . $dimension['h'] . '_' . $imageName, $image);
        }
    }

    /**
     * Delete given file
     *
     * @param $file
     * @return bool
     */
    public static function delete($file)
    {
        if(self::storage()->has($file)) {
            self::storage()->delete($file);
            return true;
        }
        return false;
    }



}
