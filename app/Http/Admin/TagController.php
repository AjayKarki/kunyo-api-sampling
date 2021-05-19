<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Foundation\Services\PostService;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Foundation\Models\Tag;
use Foundation\Services\TagService;
use Foundation\Requests\Tag\{
    StoreRequest,
    UpdateRequest
};
use Neputer\Supports\BaseController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use function GuzzleHttp\Psr7\str;

/**
 * Class TagController
 * @package App\Http\Controllers\Admin
 */
class TagController extends BaseController
{

    /**
     * The TagService instance
     *
     * @var $tagService
     */
    private $tagService;

    /**
     * The PostService instance
     *
     * @var $postService
     */
    private $postService;

    public function __construct(
        TagService $tagService,
        PostService $postService
    )
    {
        $this->tagService = $tagService;
        $this->postService = $postService;
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
                ->of($this->tagService->filter($request->only('filter.title', 'search.value')))
                ->addColumn('created_at', function ($data) {
                    return view('admin.common.created-at',compact('data'))->render();
                })
                ->addColumn('action', function ($data) {
                    $model = 'tag';
                    return view('admin.common.data-table-action', compact('data', 'model'))->render();
                })
                ->addColumn('description', function ($data) {
                    return view('admin.common.description', compact('data'))->render();
                })
                ->addColumn('status', function ($data) {
                     return view('admin.common.status', compact('data'))->render();
                })
                ->addColumn('checkbox', function ($data) {
                    return view('admin.common.checkbox', compact('data'))->render();
                })
                ->rawColumns(['description','checkbox', 'action', 'created_at', 'status', ])
                ->make(true);
        }

        $data['status'] = $this->tagService->status();
        return view('admin.tag.index', compact('data'));
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
        return view('admin.tag.create', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreRequest $request
     * @return RedirectResponse
     */
    public function store(StoreRequest $request)
    {
        $this->tagService->new($request->merge([
            'slug' => Str::slug($request->get('tag_name'))
        ])->all());
        flash('success', 'Record successfully created.');
        return $this->redirect($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  Tag $tag
     * @return Factory
     */
    public function show(Tag $tag)
    {
        $data = [];
        $data['tag'] = $tag->loadCount('posts');
        return view('admin.tag.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Tag $tag
     * @return Factory
     */
    public function edit(Tag $tag)
    {
        $data = [];
        $data['tag']  = $tag;
        return view('admin.tag.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @param  Tag $tag
     * @return RedirectResponse
     */
    public function update(UpdateRequest $request, Tag $tag)
    {
        $this->tagService->update($request->all(), $tag);
        flash('success', 'Record successfully updated.');
        return $this->redirect($request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Tag $tag
     * @return RedirectResponse
     */
    public function destroy(Tag $tag)
    {
         $this->tagService->delete($tag);
         flash('success', 'Record is deleted successfully !');
        return redirect('admin/tag');

    }
}
