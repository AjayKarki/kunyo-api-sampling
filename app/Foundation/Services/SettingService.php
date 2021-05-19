<?php

namespace Foundation\Services;

use Exception;
use Foundation\Models\Setting;
use Illuminate\Database\DatabaseManager;
use Neputer\Supports\BaseService;
use Neputer\Supports\Mixins\Image;

/**
 * Class SettingService
 * @package Foundation\Services
 */
class SettingService
{
    use Image;

    /**
     * The settings folder to store photo
     *
     * @var string
     *
     */

    protected $folder = 'setting';


    /**
     * The Setting instance
     *
     * @var $model
     */
    protected $model;

    /**
     * @var DatabaseManager
     */
    private $database;

    /**
     * SettingService constructor.
     * @param Setting $setting
     * @param DatabaseManager $databaseManager
     */
    public function __construct(Setting $setting, DatabaseManager $databaseManager)
    {
        $this->model = $setting;
        $this->database = $databaseManager;
    }

    /**
     * Update the website settings
     *
     * @param array $data
     * @param $model
     * @return bool|mixed|void
     * @throws \Exception
     */
    public function update(array $data)
    {
        try {
            $this->database->beginTransaction();

            if (isset($data['photo'])){
                $logo_row = $this->model->where('key', 'logo')->first();

                if ($logo_row) {
                    $image_name = $this->uploadImage($data['photo'], $this->folder, $logo_row->value);
                    $logo_row->value = $image_name;
                    $logo_row->save();
                }
            }

            foreach ($data as $key => $value) {
                $this->model->updateOrCreate([ 'key' => $key, ], [
                    'key'        => $key,
                    'value'      => is_iterable($value) ? json_encode($value) : $value,
                    'updated_by' => auth()->id(),
                ]);
            }

            $this->database->commit();
            return true;
        } catch (Exception $exception) {
            $this->database->rollBack();
            return;
        }
    }

    /**
     * Get the settings in array format
     *
     * @return mixed
     */
    public function getSettings()
    {
        $data = [];
        foreach ($this->pluckToArray() as $key => $value) {
            $data[$key] = is_json($value) ? json_decode($value, 1) : $value;
        }
        return $data;
    }

    /**
     * Get the settings from database
     *
     * @return mixed
     */
    public function pluckToArray()
    {
        return $this->model->pluck('value','key')->toArray();
    }

    public function pluck($key)
    {
        $value = $this->model->where('key', $key)->value('value');
        return is_json($value) ? json_decode($value, 1) : $value;
    }

}
