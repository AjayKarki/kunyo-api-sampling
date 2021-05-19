<?php

namespace Neputer\Supports\DTO;

use Illuminate\Http\Request;
use Neputer\Supports\Utility;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\Paginator;
use Illuminate\Contracts\Support\Responsable;
use Symfony\Component\HttpFoundation\Response;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\DataTransferObjectCollection;

final class ResponsePaginationData extends DataTransferObject implements Responsable
{

    use \Neputer\Supports\Mixins\Responsable;

    public Paginator $paginator;

    public DataTransferObjectCollection $collection;

    public ?int $total;

    public ?int $limit;

    /**
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function toResponse($request)
    {
        $pageCount = (int) ($this->total ?? 1) / ($this->limit ?? 1);
        return $this->responseOk(
            [
                'data' => $this->collection->toArray(),
                'meta' => $this->collection->count() ? [
                    'currentPage' => $this->paginator->url($this->paginator->currentPage()),
                    'nextPage'    => $this->paginator->nextPageUrl(),
                    // 'lastPage'    => $this->paginator->url($this->paginator->lastPage()),
                    'path'        => $this->paginator->path(),
                    'perPage'     => $this->paginator->perPage(),
                    'total'       => $this->total ?? $this->collection->count(),
                    'pageCount'   => (int) (Utility::is_decimal($pageCount) ? ($pageCount + 1) : $pageCount),
                ] : null,
            ]
        );
    }
}
