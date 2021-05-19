<?php

namespace Foundation\Services;

use Foundation\Builders\Filters\Faq\Filter;
use Foundation\Models\Faq;
use Neputer\Config\Status;
use Neputer\Supports\BaseService;

/**
 * Class FaqService
 * @package Foundation\Services
 */
class FaqService extends BaseService
{

    /**
     * The Faq instance
     *
     * @var $model
     */
    protected $model;

    /**
     * FaqService constructor.
     * @param Faq $faq
     */
    public function __construct(Faq $faq)
    {
        $this->model = $faq;
    }

    /**
     * Filter
     *
     * @param array|null $data
     * @return mixed
     */
    public function filter(array $data = null)
    {
        return Filter::apply(
            $this->model
                ->select('id','faq_name','body','status', 'type','created_at'),$data)
            ->orderBy('id', 'DESC');
    }

}
