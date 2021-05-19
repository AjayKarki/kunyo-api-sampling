<?php

declare(strict_types=1);

namespace Neputer\Supports\DTO;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Support\Responsable;
use Symfony\Component\HttpFoundation\Response;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\DataTransferObjectCollection;

final class ResponseData extends DataTransferObject implements Responsable
{

        use \Neputer\Supports\Mixins\Responsable;

    public int $status = 200;

//    /** @var DataTransferObject|DataTransferObjectCollection */
    public $data;

    /**
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function toResponse($request)
    {
        return $this->responseOk(
            [
                'data' => $this->data->toArray(),
            ]
        );
    }
}
