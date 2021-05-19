<?php

namespace Modules\Application\Http\DTOs\Cms;

use Foundation\Lib\PostType;
use Foundation\Models\Post;
use Spatie\DataTransferObject\DataTransferObject;

final class PostData extends DataTransferObject
{

    public string $title;

    public ?string $description;

    public string $type;

    public ?string $banner;

    public ?string $seo_title;

    public ?string $seo_desc;

    public ?string $seo_keywords;

    public static function fromModel(Post $post): DataTransferObject
    {
        return new self([
            'title'                 => $post->title,
            'description'           => $post->content,
            'type'                  => PostType::$current[$post->post_type] ?? 'N/A',
            'banner'                => get_image_url('post', $post->image),
            'seo_title'             => $post->seo_title,
            'seo_desc'              => $post->seo_desc,
            'seo_keywords'          => $post->seo_keywords,
        ]);
    }

}
