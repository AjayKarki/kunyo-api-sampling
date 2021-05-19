<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Foundation\Models\Game;
use Foundation\Requests\Game\StoreRequest;
use Foundation\Requests\Game\UpdateRequest;
use Foundation\Services\CategoryService;
use Foundation\Services\GameService;
use Illuminate\Http\Request;
use Neputer\Supports\BaseController;
use Neputer\Supports\Mixins\Image;

class GameController extends BaseController
{
    use Image;
    /**
     * The GameService instance
     *
     * @var $gameService
     */
    private $gameService;

    /**
     * The CategoryService instance
     *
     * @var $categoryService
     */
    private $categoryService;

    private $folder = 'game';

    public function __construct(GameService $gameService, CategoryService $categoryService)
    {
        $this->gameService = $gameService;
        $this->categoryService = $categoryService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return datatables()
                ->of($this->gameService->filter($request->only('filter', 'search.value')))
                ->addColumn('created_at', function ($data) {
                    return view('admin.common.created-at', compact('data'))->render();
                })
                ->addColumn('image', function ($data) {
                    $folder = $this->folder;
                    $image_name = $data->image;
                    return view('admin.common.image.image-preview', compact('folder', 'image_name'));
                })
                ->addColumn('action', function ($data) {
                    $model = 'game';
                    return view('admin.common.data-table-action', compact('data', 'model'))->render();
                })
                ->addColumn('status', function ($data) {
                    return view('admin.common.status', compact('data'))->render();
                })
                ->addColumn('checkbox', function ($data) {
                    return view('admin.common.checkbox', compact('data'))->render();
                })
                ->rawColumns(['name', 'checkbox', 'status', 'action', 'created_at',])
                ->make(true);
        }

        $data['status'] = $this->gameService->status();
        $data['category'] = $this->categoryService->getCategory();
        return view('admin.game.index', compact('data'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $data = [];
        $data['category'] = $this->categoryService->getCategory();

        return view('admin.game.create', compact('data'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreRequest $request)
    {
        if ($request->hasFile('display_picture')) {
            $imageName = $this->uploadImage($request->file('display_picture'), $this->folder);
        }
        $this->gameService->new($request->merge([
            'image' => $imageName ?? null,
        ])->all());

        flash('success', 'Record successfully created.');
        return $this->redirect($request);
    }

    /**
     * @param Game $game
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Game $game)
    {
        $data = [];
        $data['game']  = $game;
        return view('admin.game.show', compact('data'));
    }

    /**
     * @param Game $game
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Game $game)
    {
        $data = [];
        $data['game']  = $game;
        $data['category'] = $this->categoryService->getCategory();
        return view('admin.game.edit', compact('data'));
    }

    /**
     * @param UpdateRequest $request
     * @param Game $game
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateRequest $request, Game $game)
    {
        $data = [];
        $data['game'] = $game;

        if ($request->hasFile('display_picture')) {
            $imageName = $this->uploadImage($request->file('display_picture'), $this->folder, $game->image);
        }

        $this->gameService->update($request->merge([
            'image' => $imageName ?? $game->image,
        ])->all(), $game);

        flash('success', 'Record successfully created.');
        return $this->redirect($request);
    }

    /**
     * @param Game $game
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Game $game)
    {
        $this->gameService->delete($game);
        $this->delete('game', $game->photo);
        flash('success', 'Game is deleted successfully !');
        return redirect('admin/game');
    }
}
