<?php

namespace Foundation\Services;

use Foundation\Models\Genre;
use Neputer\Supports\BaseService;

/**
 * Class GenreService
 * @package Foundation\Services
 */
class GenreService extends BaseService
{

    /**
     * The Genre instance
     *
     * @var $model
     */
    protected $model;

    /**
     * GenreService constructor.
     * @param Genre $genre
     */
    public function __construct(Genre $genre)
    {
        $this->model = $genre;
    }

    /**
     * Filter
     *
     * @param string|null $name
     * @return mixed
     */
    public function filter(string $name = null)
    {
        return $this->model
            ->where(function ($query) use ($name){
                if($name){
                    $query->where('name','like', '%'. $name .'%');
                }
            })
            ->latest();
    }

}
