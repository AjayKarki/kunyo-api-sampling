<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Foundation\Models\Genre;
use Neputer\Supports\BaseController;
use Foundation\Requests\Genre\{
    StoreRequest,
    UpdateRequest
};
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use Foundation\Services\GenreService;

/**
 * Class GenreController
 * @package App\Http\Controllers\Admin
 */
class GenreController extends BaseController
{

    /**
     * The GenreService instance
     *
     * @var $genreService
     */
    private $genreService;

    public function __construct(GenreService $genreService)
    {
        $this->genreService = $genreService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Factory|View
     * @throws Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return datatables()
                ->of($this->genreService->filter($request->input('search.value')))
                ->addColumn('created_at', function ($data) {
                    return $data->created_at . " <code>{$data->created_at->diffForHumans()}</code>";
                })
                ->addColumn('action', function ($data) {
                    $model = 'genre';
                    return view('admin.common.data-table-action', compact('data', 'model'))->render();
                })
                ->addColumn('status', function ($data) {
                     return view('admin.common.status', compact('data'))->render();
                })
                ->addColumn('checkbox', function ($data) {
                    return view('admin.common.checkbox', compact('data'))->render();
                })
                ->rawColumns([ 'action', 'created_at', 'status', 'checkbox'])
                ->make(true);
        }

        return view('admin.genre.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Factory
     */
    public function create()
    {
        $data = [];
        //
        return view('admin.genre.create', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreRequest $request
     * @return RedirectResponse
     */
    public function store(StoreRequest $request)
    {
        $this->genreService->new($request->all());
        flash('success', 'Record successfully created.');
        return $this->redirect($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  Genre $genre
     * @return Factory
     */
    public function show(Genre $genre)
    {
        $data = [];
        $data['genre'] = $genre;
        return view('admin.genre.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Genre $genre
     * @return Factory
     */
    public function edit(Genre $genre)
    {
        $data = [];
        $data['genre']  = $genre;
        return view('admin.genre.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @param  Genre $genre
     * @return RedirectResponse
     */
    public function update(UpdateRequest $request, Genre $genre)
    {
        $this->genreService->update($request->all(), $genre);
        flash('success', 'Record successfully updated.');
        return $this->redirect($request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Genre $genre
     * @return RedirectResponse
     */
    public function destroy(Genre $genre)
    {
        $this->genreService->delete($genre);
        flash('success', 'Genre is deleted successfully !');
        return redirect()->back();
    }
}
