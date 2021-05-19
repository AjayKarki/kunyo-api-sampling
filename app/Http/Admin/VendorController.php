<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Foundation\Models\Vendor;
use Neputer\Supports\BaseController;
use Foundation\Requests\Vendor\{
    StoreRequest,
    UpdateRequest
};
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use Foundation\Services\VendorService;

/**
 * Class VendorController
 * @package App\Http\Controllers\Admin
 */
class VendorController extends BaseController
{

    /**
     * The VendorService instance
     *
     * @var $vendorService
     */
    private $vendorService;

    public function __construct(VendorService $vendorService)
    {
        $this->vendorService = $vendorService;
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
                ->of($this->vendorService->filter($request->input('search.value')))
                ->addColumn('created_at', function ($data) {
                    return $data->created_at . " <code>{$data->created_at->diffForHumans()}</code>";
                })
                ->addColumn('action', function ($data) {
                    $model = 'vendor';
                    return view('admin.common.data-table-action', compact('data', 'model'))->render();
                })
                ->addColumn('contact', function ($data){
                    return "<b>{$data->address}</b><br><i class='fa fa-phone'></i> {$data->phone}<br><i class='fa fa-envelope'></i> {$data->email}";
                })
                ->addColumn('contact_person', function ($data){
                    return "<b>{$data->contact_person}</b><br><i class='fa fa-phone'></i> {$data->contact_person_phone}";
                })
                ->rawColumns([ 'action', 'created_at', 'contact', 'contact_person' ])
                ->make(true);
        }

        return view('admin.vendor.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Factory
     */
    public function create()
    {
        $data = [];
        return view('admin.vendor.create', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreRequest $request
     * @return RedirectResponse
     */
    public function store(StoreRequest $request)
    {
        $this->vendorService->new($request->all());
        flash('success', 'Vendor successfully created.');
        return $this->redirect($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  Vendor $vendor
     * @return Factory
     */
    public function show(Vendor $vendor)
    {
        $data = [];
        $data['vendor'] = $vendor;
        return view('admin.vendor.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Vendor $vendor
     * @return Factory
     */
    public function edit(Vendor $vendor)
    {
        $data = [];
        $data['vendor']  = $vendor;
        return view('admin.vendor.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @param  Vendor $vendor
     * @return RedirectResponse
     */
    public function update(UpdateRequest $request, Vendor $vendor)
    {
        $this->vendorService->update($request->all(), $vendor);
        flash('success', 'Record successfully updated.');
        return $this->redirect($request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Vendor $vendor
     * @return RedirectResponse
     */
    public function destroy(Vendor $vendor)
    {
        $this->vendorService->delete($vendor);
        flash('success', 'Vendor is deleted successfully !');
        return redirect()->back();
    }
}
