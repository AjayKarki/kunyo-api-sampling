<?php

namespace Neputer\Supports\Mixins;

use File;
use Neputer\Supports\Utility;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

/**
 * Trait HasImage
 * @package Neputer\Supports\Mixins
 */
trait HasImage
{

    /**
     * Will attach image for the given request
     *
     * @param $image
     * @param bool $useWaterMark
     * @param string $watermarkPosition
     * @param null $existingImage
     * @return string
     * @throws FileNotFoundException
     */
    public static function attachImage( $image, $useWaterMark = false, $watermarkPosition = 'bottom-right', $existingImage = null )
    {
        $imageWithWaterMark = null;

        $imageName  = static::getRandomNumber() . '.' . $image->getClientOriginalExtension();
        $folderName = static::getFolderName();

        $imagePath = 'images'.DIRECTORY_SEPARATOR.$folderName.DIRECTORY_SEPARATOR.$imageName;

        if ($useWaterMark) {
            if (!$watermarkPosition) {
                $watermarkPosition = 'bottom-right';
            }
            $imageWithWaterMark = \Intervention\Image\Facades\Image::make($image->getRealPath())
                ->insert(public_path('images/default-logo.png'), $watermarkPosition, 5, 5)
                ->encode($image->getClientOriginalExtension());
            $imageUpload = $imageWithWaterMark->__toString();
        } else {
            $imageUpload = File::get($image);
        }

        Storage::disk('public')->put($imagePath, $imageUpload);

        if ($useWaterMark) {
            static::attachThumbs($imageWithWaterMark, $imageName);
        } else {
            static::attachThumbs($imageUpload, $imageName);
        }

        if($existingImage) {
            if (is_array($existingImage)) {
                foreach ($existingImage as $image) {
                    static::deleteImages($image);
                }
            } else {
                static::deleteImages($existingImage);
            }
        }

        return $imageName;
    }

    /**
     * Attach thumbnails
     *
     * @param $imageFile
     * @param $imageName
     */
    public static function attachThumbs($imageFile, $imageName)
    {
        foreach (static::getImageDimensions() as $dimension) {

            $img = \Intervention\Image\Facades\Image::make($imageFile)
                ->resize($dimension['width'], $dimension['height'], function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->encode();

            Storage::disk('public')->put(
                'images'.DIRECTORY_SEPARATOR . static::getFolderName() . DIRECTORY_SEPARATOR . 'thumbs' . DIRECTORY_SEPARATOR . $dimension['width'] . '_' . $dimension['height'] . '_' . $imageName, $img);
        }
    }

    /**
     * Delete Image with its thumbnails
     *
     * @param string $image
     */
    public static function deleteImages(string $image)
    {
        static::deleteImage('images'. DIRECTORY_SEPARATOR .static::getFolderName(). DIRECTORY_SEPARATOR .$image);
        foreach (static::getImageDimensions() as $dimension) {
            static::deleteImage('images'. DIRECTORY_SEPARATOR .static::getFolderName(). DIRECTORY_SEPARATOR . 'thumbs' . DIRECTORY_SEPARATOR .$dimension['width'] . '_' . $dimension['height'] . '_' . $image);
        }
    }

    /**
     * Delete the given image
     *
     * @param $image
     * @return bool
     */
    public static function deleteImage($image)
    {
        if(Storage::disk('public')->has($image)) {
            Storage::disk('public')->delete($image);
            return true;
        }
        return false;
    }

    /**
     * Return random number to be prefix to the image name
     *
     * @return string
     */
    public static function getRandomNumber()
    {
        return Utility::generateRandomNumber();
    }

    /**
     * Return the image dimensions
     *
     * @return string[][]
     */
    public static function getImageDimensions()
    {
        return [
            [ 'width' => '200', 'height' => '200', ],
            [ 'width' => '400', 'height' => '400', ],
        ];
    }

    /**
     * Return the folder name
     *
     * @return string
     */
    public static function getFolderName()
    {
        return static::setFolderName();
    }

    public function getImage()
    {
        $filePath = 'storage/images/'.static::getFolderName().'/'. ($this->{static::getImageColumn()} ?? 'no.png');
        if (file_exists( public_path($filePath))) {
            return asset($filePath);
        }
        return config('neputer.admin.imagePath');
    }

    public function getThumbnail($width = 200, $height = 200)
    {
        $filePath = 'storage/images/'.static::getFolderName().'/thumbs/'. $width .'_'. $height .'_' .($this->{static::getImageColumn()} ?? 'no.png');
        if (file_exists( public_path($filePath))) {
            return asset($filePath);
        }
        return asset('assets/frontend/images/no_image.png');
    }

   /**
    * Return the image column
    *
    * @return string
    */
   public static function getImageColumn()
   {
       return static::setImageColumn();
   }

    /**
     * Set the folder name
     *
     * @return string
     */
    abstract public static function setFolderName() : string;

   /**
    * Set the image column
    *
    * @return string
    */
   abstract public static function setImageColumn() : string;

}
