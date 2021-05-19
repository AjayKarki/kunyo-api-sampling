<?php

namespace App\Http\Controllers\Admin\Actions;

use Foundation\Lib\Cache;
use Foundation\Lib\QuickSetting;
use Illuminate\Http\RedirectResponse;

/**
 * Class QuickSettingAction
 * @package App\Http\Controllers\Admin\Actions
 */
final class QuickSettingAction
{

    /**
     * @param string $pattern
     * @param null $value
     * @return RedirectResponse
     */
    public function __invoke(string $pattern, $value = null)
    {
        $message = null;

        switch ($pattern) {
            case QuickSetting::PATTERN_CLEAR_CACHE:
                Cache::clear();
                $message = 'The cache is cleared successfully!';
                break;
            case QuickSetting::PATTERN_CLEAR_LOG:
                static::clearLog();
                $message = 'The log is cleared successfully!';
                break;
            case QuickSetting::PATTERN_CLEAR_VIEW:
                \Artisan::call('view:clear');
                $message = 'The view is cleared successfully!';
                break;
            default:
                flash('error', 'The quick settings operation is unsuccessful.');
                return back()->with(QuickSetting::QUICK_SETTING, 'yes');
        }

        flash('success', $message);
        return back()->with(QuickSetting::QUICK_SETTING, 'yes');
    }

    public static function clearLog()
    {
        if (function_exists('exec')) {
            exec('echo "" > ' . storage_path('logs/laravel.log'));
            exec('truncate -s 0 storage/logs/*.log', $output, $result);
        }
    }

}
