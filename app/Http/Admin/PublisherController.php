<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Foundation\Models\Publisher;
use Neputer\Supports\BaseController;
use Foundation\Requests\Publisher\{
    StoreRequest,
    UpdateRequest
};
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use Foundation\Services\PublisherService;

/**
 * Class PublisherController
 * @package App\Http\Controllers\Admin
 */
class PublisherController extends BaseController
{

    /**
     * The PublisherService instance
     *
     * @var $publisherService
     */
    private $publisherService;

    public function __construct(PublisherService $publisherService)
    {
        $this->publisherService = $publisherService;
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
                ->of($this->publisherService->filter($request->input('search.value')))
                ->addColumn('created_at', function ($data) {
                    return $data->created_at . " <code>{$data->created_at->diffForHumans()}</code>";
                })
                ->addColumn('action', function ($data) {
                    $model = 'publisher';
                    return view('admin.common.data-table-action', compact('data', 'model'))->render();
                })
                ->addColumn('status', function ($data) {
                     return view('admin.common.status', compact('data'))->render();
                })
                ->addColumn('checkbox', function ($data) {
                    return view('admin.common.checkbox', compact('data'))->render();
                })
                ->rawColumns([ 'action', 'created_at', 'status', 'checkbox' ])
                ->make(true);
        }

        return view('admin.publisher.index');
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
        return view('admin.publisher.create', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreRequest $request
     * @return RedirectResponse
     */
    public function store(StoreRequest $request)
    {
        $this->publisherService->new($request->all());
        flash('success', 'Record successfully created.');
        return $this->redirect($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  Publisher $publisher
     * @return Factory
     */
    public function show(Publisher $publisher)
    {
        $data = [];
        $data['publisher'] = $publisher;
        return view('admin.publisher.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Publisher $publisher
     * @return Factory
     */
    public function edit(Publisher $publisher)
    {
        $data = [];
        $data['publisher']  = $publisher;
        return view('admin.publisher.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @param  Publisher $publisher
     * @return RedirectResponse
     */
    public function update(UpdateRequest $request, Publisher $publisher)
    {
        $this->publisherService->update($request->all(), $publisher);
        flash('success', 'Record successfully updated.');
        return $this->redirect($request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Publisher $publisher
     * @return RedirectResponse
     */
    public function destroy(Publisher $publisher)
    {
        $this->publisherService->delete($publisher);
        flash('success', 'Publisher is deleted successfully !');
        return redirect()->back();
    }
}
