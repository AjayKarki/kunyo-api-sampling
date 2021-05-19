<?php

namespace Foundation\Services;

use Neputer\Config\Status;
use Foundation\Models\Post;
use Foundation\Lib\PostType;
use Neputer\Supports\BaseService;
use Foundation\Builders\Filters\Post\Filter;

/**
 * Class PostService
 * @package Foundation\Services
 */
class PostService extends BaseService
{

    /**
     * The Post instance
     *
     * @var $model
     */
    protected $model;

    /**
     * PostService constructor.
     * @param Post $post
     */
    public function __construct(Post $post)
    {
        $this->model = $post;
    }

    /**
     * Filter
     *
     * @param array|null $data
     * @return mixed
     */
    public function filter(array $data = null)
    {
        return Filter::apply($this->model
                ->select('posts.*'), $data)
                ->withCount('tags')
                ->with('user:id,first_name')
                ->orderBy('id', 'DESC');
    }

    public function getPostTag()
    {
        return $this->model
            ->with('tags')
            ->get();
    }


    /**
     * Return posts for given category
     *
     * @param $categoryId
     * @param int $limit
     * @return mixed
     */
    public function getByCategory( $categoryId, $limit = 5 )
    {
        return $this->model
            ->select(
                'posts.*', 'author.email as author_email', 'categories.category_name'
            )
            ->selectRaw("CONCAT_WS(' ', author.first_name, author.middle_name, author.last_name) AS author_full_name")
            ->leftJoin('categories', 'categories.id', '=', 'posts.category_id')
            ->leftJoin('users as author', 'author.id', '=', 'posts.created_by')
            ->where('categories.id', $categoryId)
            ->limit($limit)
            ->orderby('posts.id', 'DESC')
            ->get();
    }

    public function getByType($type, $limit)
    {
        return $this->model
            ->select( 'posts.*', 'author.email as author_email', 'categories.category_name' )
            ->selectRaw("CONCAT_WS(' ', author.first_name, author.middle_name, author.last_name) AS author_full_name")
            ->leftJoin('categories', 'categories.id', '=', 'posts.category_id')
            ->leftJoin('users as author', 'author.id', '=', 'posts.created_by')
            ->where('posts.type', $type)
            ->limit($limit)
            ->orderby('posts.id', 'DESC')
            ->get();
    }

    public function getByPostType($postType, $limit)
    {
        return $this->model
            ->select( 'posts.*', 'author.email as author_email', 'categories.category_name' )
            ->selectRaw("CONCAT_WS(' ', author.first_name, author.middle_name, author.last_name) AS author_full_name")
            ->leftJoin('categories', 'categories.id', '=', 'posts.category_id')
            ->leftJoin('users as author', 'author.id', '=', 'posts.created_by')
            ->where('posts.post_type', $postType)
            ->limit($limit)
            ->orderby('posts.id', 'DESC')
            ->get();
    }

    public function getByViews($limit)
    {
        return $this->model
            ->select(
                'posts.*', 'author.email as author_email', 'categories.category_name'
            )
            ->selectRaw("CONCAT_WS(' ', author.first_name, author.middle_name, author.last_name) AS author_full_name")
            ->leftJoin('categories', 'categories.id', '=', 'posts.category_id')
            ->leftJoin('users as author', 'author.id', '=', 'posts.created_by')
            ->limit($limit)
            ->orderby('posts.views', 'DESC')
            ->get();
    }

    /**
     * Get single or multiple posts
     *
     * @param string|null $slug
     * @param null $paginate
     * @return mixed
     */
    public function getPost(string $slug = null, $paginate = null)
    {
        $query = $this->model
            ->select( 'posts.*', 'author.email as author_email', 'author.image as author_photo', 'categories.category_name' )
            ->selectRaw("CONCAT_WS(' ', author.first_name, author.middle_name, author.last_name) AS author_full_name")
            ->leftJoin('categories', 'categories.id', '=', 'posts.category_id')
            ->leftJoin('users as author', 'author.id', '=', 'posts.created_by')
            ->with('tags')
            ->where('posts.post_type', PostType::POST_TYPE_POST);

        if ($slug)
            return $query->where('posts.slug', $slug)->first();

        if (is_null($paginate)) {
            return $query->latest()
                ->get();
        }
        return $query->latest()->paginate($paginate);
    }

    /**
     * Increment Posts view
     *
     * @param $post
     * @return mixed
     */
    public function incrementViews($post)
    {
        if (is_null($post->views))
            return $this->model->where('id', $post->id)->update(['views' => 1]);
        else
            return $post->increment('views');
    }

    public function doesPageExists($slug): bool
    {
        return $this->model
            ->where('post_type', PostType::POST_TYPE_PAGE)
            ->where('slug', $slug)
            ->exists();
    }

    public function retrieve($slug, $postType = PostType::POST_TYPE_PAGE)
    {
        return $this->model
            ->where('post_type', PostType::POST_TYPE_PAGE)
            ->where('slug', $slug)
            ->first();
    }

}
