<?php

namespace App\Http\Controllers\Admin\Appearance\Actions\Menus;

use Illuminate\Http\Request;
use Illuminate\View\Factory;
use Foundation\Services\NavService;
use Neputer\Supports\BaseController;

/**
 * Class RenderMenu
 * @package App\Http\Controllers\Admin\Appearance\Actions\Menus
 */
final class RenderMenu extends BaseController
{

    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var NavService
     */
    private $navService;

    /**
     * RenderMenu constructor.
     *
     * @param Factory $factory
     * @param NavService $navService
     */
    public function __construct(
        Factory $factory,
        NavService $navService
    )
    {
        $this->factory = $factory;
        $this->navService = $navService;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function __invoke(Request $request)
    {
        if ($request->has('section')) {
            $section = $request->get('section');
            $menus = $this->navService->bySection($section);

            return $this->responseOk(
                $this->factory->make('admin.appearance.menu.partials.list', compact('menus', 'section'))
                    ->render()
            );
        }
    }

}
