<?php

if (! function_exists('cart') ) :

    function cart() {
        return app(\Neputer\Supports\Cart\Cart::class);
    }

endif;

if (! function_exists('get_image_url') ) :

    /**
     * @deprecated
     */
    function get_image_url($folder, $imageName)
    {
        $filePath = 'storage'. DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $imageName;
        if (file_exists(public_path(). DIRECTORY_SEPARATOR . $filePath)) {
            if (!is_null($imageName)) {
                return asset($filePath);
            } else {
                return asset('images/no_image.png');
            }
        } else {
            return asset('images/no_image.png');
        }
    }

endif;
