<?php

namespace Modules\Application\Http\Controllers\Cms;

use Foundation\Services\PostService;
use Illuminate\Http\Response;
use Modules\Application\Http\Controllers\BaseController;
use Modules\Application\Http\DTOs\Cms\PostData;

final class PageAction extends BaseController
{

    /**
     * @var PostService
     */
    private PostService $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function __invoke($slug)
    {
        $page = $this->postService->retrieve($slug);

        if (!$page) {
            return $this->responseError($page, Response::HTTP_NOT_FOUND, 'Page Not Found !');
        }

        return $this->responseOk(
            PostData::fromModel($page)
        );
    }

}
