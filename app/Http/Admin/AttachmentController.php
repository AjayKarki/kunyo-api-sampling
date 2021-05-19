<?php


namespace App\Http\Controllers\Admin;

use Foundation\Models\Image;
use Foundation\Services\ImageService;

/**
 * Class AttachmentController
 * @package App\Http\Controllers\Admin
 */
class AttachmentController
{
    /**
     * @var ImageService
     */
    private $imageService;

    /**
     * AttachmentController constructor.
     * @param ImageService $imageService
     */
    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Delete a Image
     *
     * @param Image $image
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Image $image)
    {
        $this->imageService->remove([$image->path]);
        flash('success', 'Attachment Deleted');
        return redirect()->back();
    }

}
