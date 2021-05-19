<?php

namespace Foundation\Services;

use Foundation\Builders\Filters\Game\Filter;
use Foundation\Models\Game;
use Neputer\Config\Status;
use Neputer\Supports\BaseService;

/**
 * Class GameService
 * @package Foundation\Services
 */
class GameService extends BaseService
{

    /**
     * The Game instance
     *
     * @var $model
     */
    protected $model;

    /**
     * GameService constructor.
     * @param Game $game
     */
    public function __construct(Game $game)
    {
        $this->model = $game;
    }

    /**
     * Filter
     *
     * @param array|null $data
     * @return mixed
     */
    public function filter(array $data = null)
    {
        return Filter::apply(
            $this->model->newQuery(), $data)
            ->latest();
    }

}
