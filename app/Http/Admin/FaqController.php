<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Foundation\Models\Faq;
use Neputer\Supports\BaseController;
use Foundation\Requests\Faq\{
    StoreRequest,
    UpdateRequest
};
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use Foundation\Services\FaqService;

/**
 * Class FaqController
 * @package App\Http\Controllers\Admin
 */
class FaqController extends BaseController
{

    /**
     * The FaqService instance
     *
     * @var $faqService
     */
    private $faqService;

    public function __construct(FaqService $faqService)
    {
        $this->faqService = $faqService;
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
                ->of($this->faqService->filter($request->only('filter', 'search.value')))
                ->addColumn('created_at', function ($data) {
                    return view('admin.common.created-at',compact('data'))->render();
                })
                ->addColumn('body', function ($data) {
                    $data->description = $data->body;
                    return view('admin.common.description', compact('data'))->render();
                })
                ->addColumn('action', function ($data) {
                    $model = 'faq';
                    return view('admin.common.data-table-action', compact('data', 'model'))->render();
                })
                ->addColumn('status', function ($data) {
                     return view('admin.common.status', compact('data'))->render();
                })
                ->addColumn('checkbox', function ($data) {
                    return view('admin.common.checkbox', compact('data'))->render();
                })
                ->addColumn('type', function ($data) {
                    return '<small><b>' . \Foundation\Lib\Faq::$types[$data->type] ?? 'General' . '</b></small>';
                })
                ->rawColumns(['body','checkbox','status', 'action', 'created_at', 'type', ])
                ->make(true);
        }

        $data['status'] = $this->faqService->status();
        return view('admin.faq.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Factory
     */
    public function create()
    {
        $data['types'] = \Foundation\Lib\Faq::getTypes();
        return view('admin.faq.create', 'data');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreRequest $request
     * @return RedirectResponse
     */
    public function store(StoreRequest $request)
    {
        $this->faqService->new($request->merge([
            'slug' => Str::slug($request->get('faq_name'))
        ])->all());
        flash('success', 'Record successfully created.');
        return $this->redirect($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  Faq $faq
     * @return Factory
     */
    public function show(Faq $faq)
    {
        $data = [];
        $data['faq'] = $faq;
        $data['types'] = \Foundation\Lib\Faq::getTypes();
        return view('admin.faq.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Faq $faq
     * @return Factory
     */
    public function edit(Faq $faq)
    {
        $data = [];
        $data['faq']  = $faq;
        $data['types'] = \Foundation\Lib\Faq::getTypes();

        return view('admin.faq.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @param  Faq $faq
     * @return RedirectResponse
     */
    public function update(UpdateRequest $request, Faq $faq)
    {
        $this->faqService->update($request->all(), $faq);
        flash('success', 'Record successfully updated.');
        return $this->redirect($request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Faq $faq
     * @return RedirectResponse
     */
    public function destroy(Faq $faq)
    {
         $this->faqService->delete($faq);
         flash('success', 'Faq is deleted successfully !');
         return redirect('admin/faq');
    }
}
