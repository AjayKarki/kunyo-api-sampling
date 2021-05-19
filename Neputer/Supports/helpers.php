<?php

if (!function_exists('neputer_version')) :

    function neputer_version ()
    {
        return '1.2.12';
    }

endif;

if (!function_exists('flash')) :

    function flash($type, $message)
    {
        return app(\Neputer\Lib\Flash::class)->notify($type, $message);
    }

endif;

if (!function_exists('is_active')) :

    /**
     * Check if given route is active
     *
     * @return boolean
     */
    function is_active(string $route)
    {
        return request()->route()->getName() === $route;
    }

endif;

if (!function_exists('is_json')) :

    /**
     * @deprecated
     */
    function is_json($string)
    {
        return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }

endif;

if (!function_exists('toaster')) :

    function toaster()
    {
        if (session()->has('notify')) {
            $type = session()->get('notify.type');
            $response = session()->get('notify.response');
            $title = '';
            $options = json_encode(\Neputer\Supports\Utility::flashConfig());
            return "toastr.$type('$response', '$title', $options);";
        }
    }

endif;

if (!function_exists('format')) :

    function format($date, $format = 'Y-m-d H:i:s')
    {
        return $date instanceof \Carbon\Carbon ? $date->format($format) : \Carbon\Carbon::parse($date)->format($format);
    }

endif;

if(!function_exists('generateRandomString')){
    function generateRandomString($length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

if (! function_exists('theme_path') ) :

    /**
     * Get the path to the themes directory.
     *
     * @param  string  $path
     * @return string
     */
    function theme_path ($path = '') {
        return app()->resourcePath('themes'.($path ? DIRECTORY_SEPARATOR.$path : $path));
    }

endif;

if (! function_exists('module_path') ) :

    /**
     * Get the path to the modules directory.
     *
     * @param  string  $path
     * @return string
     */
    function module_path ($path = '') {
        return app()->basePath('Modules'.($path ? DIRECTORY_SEPARATOR.$path : $path));
    }

endif;

if (! function_exists('active_theme') ) :

    /**
     * Get the active theme
     *
     * @param  string  $path
     * @return string
     */
    function active_theme () {
        return \Neputer\Supports\Theme\Theme::active();
    }

endif;

if (! function_exists('theme_asset') ) :

    /**
     * Get the active theme asset path
     *
     * @param  string  $path
     * @return string
     */
    function theme_asset($path = '/') {
        return \Neputer\Supports\Theme\Theme::asset($path);
    }

endif;

if (! function_exists('_x') ) :

    /**
     * Translate the given message.
     *
     * @param  string|null  $key
     * @param  array  $replace
     * @param  string|null  $locale
     * @return \Illuminate\Contracts\Translation\Translator|string|array|null
     */
    function _x($key = null, $replace = [], $locale = null) {
        return trans(\Neputer\Supports\Theme\Theme::active(). '::'. $key, $replace, $locale);
    }

endif;

if (! function_exists('write_log') ) :

    function write_log ($message, $channel = 'payment-status') {
        $logger = logger()->channel($channel)->getLogger();
        $logger->info($message);
    }

endif;

if (! function_exists('database_size') ) :

    function database_size() {
        $tableName = config('database.connections.mysql.database');
        $sqlQuery = "SELECT table_schema '$tableName', SUM( data_length + index_length) / 1024 / 1024 'db_size_in_mb' FROM information_schema.TABLES WHERE table_schema='$tableName' GROUP BY table_schema;";

        $result = app('db')->select($sqlQuery);

        return isset($result[0]->db_size_in_mb) ? $result[0]->db_size_in_mb : 0;
    }

endif;

if (! function_exists('log_file_size') ) :

    function log_file_size() {
        $size = 0;
        foreach (glob(storage_path('logs/*.log')) as $log) {
            $size += filesize($log);
        }

        if ($size >= 1073741824)
        {
            $size = number_format($size / 1073741824, 2) . ' GB';
        }
        elseif ($size >= 1048576)
        {
            $size = number_format($size / 1048576, 2) . ' MB';
        }
        elseif ($size >= 1024)
        {
            $size = number_format($size / 1024, 2) . ' KB';
        }
        elseif ($size > 1)
        {
            $size = $size . ' bytes';
        }
        elseif ($size == 1)
        {
            $size = $size . ' byte';
        }
        else
        {
            $size = '0 bytes';
        }

        return $size;
    }

endif;

function nrp($num, $decimal = 2){
    $sign = '';
    if($num < 0){
        $sign = '-';
        $num = abs($num);
    }

    if($decimal == 0)
        $trail = '';
    else{
        if((int)$num == $num)
            $trail = '.00';
        else
            $trail = substr(round($num, $decimal), (strpos($num, '.')));
    }

    $num = (int)$num;
    if($num <= 999)
        return $sign . $num . $trail;

    $result = strrev(',' . substr($num, -3));

    $number = strrev(substr($num, 0, -3));
    $digits = str_split($number);

    foreach ($digits as $i => $digit){
        $result .= $digit;
        if ($i%2 !== 0)
            $result .= ',';
    }
    $result = strrev($result);
    return $sign . ltrim($result, ',') . $trail;
}
