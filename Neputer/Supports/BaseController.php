<?php

namespace Neputer\Supports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Neputer\Supports\Access\HasAccess;
use Neputer\Supports\Mixins\Responsable;

/**
 * Class BaseController
 *
 * @package Neputer\Supports
 */
abstract class BaseController extends Controller
{

    use Responsable, HasAccess;

    protected $pagination_limit = 10;

    /**
     * Redirect if save & continue
     *
     * @param Request $request
     * @return RedirectResponse
     */
    protected function redirect(Request $request): RedirectResponse
    {
        if ($request->has('submit_continue')) {
            return back();
        }

        return redirect()->route( pathinfo($request->route()->getName(), PATHINFO_FILENAME).'.index');
    }

    public function getPaginationLimit(): int
    {
        return $this->pagination_limit;
    }

}
