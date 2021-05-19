<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Foundation\Lib\Category as CategoryType;
use Illuminate\View\View;
use Foundation\Models\Post;
use Illuminate\Http\Request;
use Foundation\Lib\PostType;
use Neputer\Supports\Mixins\Image;
use Foundation\Requests\Post\{
    StoreRequest,
    UpdateRequest
};
use Foundation\Services\TagService;
use Foundation\Services\PostService;
use Neputer\Supports\BaseController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use Foundation\Services\CategoryService;

/**
 * Class PostController
 * @package App\Http\Controllers\Admin
 */
class PostController extends BaseController
{
    use Image;

    /**
     * The PostService instance
     *
     * @var $postService
     */
    private $postService;

    /**
     * @var TagService
     */
    private $tagService;

    /**
     * @var CategoryService
     */
    private $categoryService;
    private $folder = 'post';

    public function __construct(
        PostService $postService,
        TagService $tagService,
        CategoryService $categoryService
    )
    {
        $this->postService = $postService;
        $this->tagService = $tagService;
        $this->categoryService = $categoryService;
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
                ->of($this->postService->filter($request->only('filter', 'search.value')))
                ->addColumn('created_by', function ($data) {
                    return $data->user->first_name;
                })
                ->addColumn('title', function ($data) {
                    return $data->title . " <span class='count-flag' title='Tag Count'>{$data->tags_count}</span>";
                })
                ->addColumn('post_type', function ($data) {
                    return '<b>' . ucfirst(PostType::$current[$data->post_type]) .'</b>';
                })
                ->addColumn('action', function ($data) {
                    $model = 'post';
                    return view('admin.common.data-table-action', compact('data', 'model'))->render();
                })
                ->addColumn('status', function ($data) {
                    return view('admin.common.status', compact('data'))->render();
                })
                ->addColumn('checkbox', function ($data) {
                    return view('admin.common.checkbox', compact('data'))->render();
                })
                ->rawColumns(['title', 'post_type', 'content', 'checkbox', 'status', 'views', 'action', 'created_by',])
                ->make(true);
        }

        $data['status'] = $this->postService->status();
        return view('admin.post.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Factory
     */
    public function create()
    {
        $data = [];
        $data['post-type'] = PostType::$current;
        $data['tag'] = $this->tagService->tag();
        $data['categories'] = $this->categoryService->getCategory(CategoryType::TYPE_GENERAL_CATEGORY, false);
        return view('admin.post.create', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return RedirectResponse
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function store(StoreRequest $request)
    {
        $imageName = null;
        if ($request->hasFile('photo')) {
            $imageName = $this->uploadImage($request->file('photo'), $this->folder);
        }

        $post = $this->postService->new($request->merge([
            'image' => $imageName,
            'created_by' => auth()->id(),
            'category_id' => $request->get('category'),
            'sub_category_id' => $request->get('sub-category') ?? null,
        ])->all());

        if ($post) {
            $this->postService->syncData($post, $request->get('tags'));
        }
        flash('success', 'Record successfully created.');
        return $this->redirect($request);
    }

    /**
     * Display the specified resource.
     *
     * @param Post $post
     * @return Factory
     */
    public function show(Post $post)
    {
        $data = [];
        $data['post'] = $post->loadCount('tags')->load([
            'tags' => function ($query) {
                return $query->select('tags.id', 'tags.tag_name');
            }
        ])->loadCount('category');
        $data['parents'] = $this->categoryService->getParentTree($data['post']->category_id);
        return view('admin.post.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Post $post
     * @return Factory
     */
    public function edit(Post $post)
    {
        $data = [];
        $data['post'] = $post;
        $data['post-type'] = PostType::$current;
        $data['tag'] = $this->tagService->tag();
        $data['categories'] = $this->categoryService->getCategory(CategoryType::TYPE_GENERAL_CATEGORY, false);

        return view('admin.post.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param Post $post
     * @return RedirectResponse
     */
    public function update(UpdateRequest $request, Post $post)
    {

        $imageName = $post->image;
        if ($request->hasFile('photo')) {
            $imageName = $this->uploadImage($request->file('photo'), $this->folder, $imageName);
        }

        if ($post = $this->postService->update($request->merge([
            'image' => $imageName,
            'category_id' => $request->get('category'),
            'sub_category_id' => $request->get('sub-category') ?? null,
        ])->all(), $post)) {
            $this->postService->syncData($post, $request->get('tags'));
        }
        flash('success', 'Record successfully updated.');
        return $this->redirect($request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Post $post
     * @return RedirectResponse
     */
    public function destroy(Post $post)
    {
        $this->postService->delete($post);
        flash('success', 'Post is deleted successfully !');
        return redirect('admin/post');
    }

    public function renderSubCategory(Request $request)
    {
        $subCategory = $this->categoryService->getChild($request->get('categoryId'));
        $childCategory = $request->get('childCategory');
//        dd($subCategory, $childCategory);
        if (!$subCategory->isEmpty()) {
            $data = view('admin.post.partials.sub-category', compact('subCategory', 'childCategory'))->render();
            return response()->json($data);
        }
    }
}
