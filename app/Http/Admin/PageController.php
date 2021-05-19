<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Foundation\Models\Page;
use Foundation\Requests\Page\StoreRequest;
use Foundation\Requests\Page\UpdateRequest;
use Foundation\Services\PageService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Neputer\Supports\BaseController;
use Neputer\Supports\Mixins\Image;

class PageController extends BaseController
{
    use Image;
    /**
     * The PageService instance
     *
     * @var $pageService
     */
    private $pageService;

    protected $folder = 'page';

    /**
     * PageController constructor.
     * @param PageService $pageService
     */
    public function __construct(PageService $pageService)
    {
        $this->pageService = $pageService;
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
                ->of($this->pageService->filter($request->only('filter', 'search.value')))
                ->addColumn('action', function ($data) {
                    $model = 'page';
                    return view('admin.common.data-table-action', compact('data', 'model'))->render();
                })
                ->addColumn('title', function ($data) {
                    return $data->title;
                })
                ->addColumn('page_type', function ($data) {
                    return $data->page_type;
                })
                ->addColumn('status', function ($data) {
                    return view('admin.common.status', compact('data'))->render();
                })
                ->addColumn('checkbox', function ($data) {
                    return view('admin.common.checkbox', compact('data'))->render();
                })
                ->rawColumns(['checkbox', 'status', 'action', 'title'])
                ->make(true);
        }

        $data = [];
        $data['status'] = $this->pageService->status();
        return view('admin.page.index', compact('data'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $data = [];
        return view('admin.page.create', compact('data'));
    }

    /**
     * @param StoreRequest $request
     */
    public function store(StoreRequest $request)
    {
        $imageName = null;
        if ($request->hasFile('photo')) {
            $imageName = $this->uploadImage($request->file('photo'), $this->folder);
        }

        $this->pageService->new($request->merge([
            'page_image' => $imageName,
        ])->all());

        flash('success', 'Record successfully created.');
        return $this->redirect($request);

    }

    /**
     * @param Page $page
     */
    public function show(Page $page)
    {
        $data = [];
        $data['page'] = $page;

        return view('admin.page.show', compact('data'));
    }

    /**
     * @param Page $page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Page $page)
    {
        $data = [];
        $data['page'] = $page;
        return view('admin.page.edit', compact('data'));
    }

    /**
     * @param UpdateRequest $request
     * @param Page $page
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateRequest $request, Page $page)
    {
        $imageName = $page->page_image;
        if ($request->hasFile('photo')) {
            $imageName = $this->uploadImage($request->file('photo'), $this->folder, $imageName);
        }

        if ($request->get('page_type') == 'link') {
            $link = $request->get('link');
            $content = null;
            $page_image = null;
            if (!is_null($imageName)) {
                $this->delete($this->folder, $imageName);
            }
        } else {
            $content = $request->get('content');
            $link = null;
            $page_image = $imageName;
        }

        $this->pageService->update($request->merge([
            'page_image' => $page_image,
            'link' => $link,
            'content' => $content,
        ])->all(), $page);

        flash('success', 'Record updated successfully!!');
        return $this->redirect($request);
    }

    /**
     * @param Page $page
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Page $page)
    {
        $this->pageService->delete($page);
        flash('success', 'Domain is deleted successfully !');
        return redirect('admin/page');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTitleSlug(Request $request)
    {

        if ($request->has('title') && $request->get('title')) {
            $slug = Str::slug($request->get('title'));
            if ($this->pageService->checkForTitle($request->get('title')) == 0) {
                $response = [];
                $response['slug'] = $slug;

                return response()->json($response);
            } else {
                $response = [];
                $response['error'] = true;
                $response['message'] = $slug . ' already exist.';

                return response()->json($response);
            }
        }
    }
}
