<?php

namespace App\Http\Controllers\Admin\Actions;

use Illuminate\Http\Request;
use Neputer\Supports\BaseController;
use Foundation\Services\PostService;

final class PageExistsAction extends BaseController
{

    /**
     * @var PostService
     */
    private PostService $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function __invoke(Request $request)
    {
        return $this->responseOk(
            $this->postService->doesPageExists($request->get('slug'))
        );
    }

}
