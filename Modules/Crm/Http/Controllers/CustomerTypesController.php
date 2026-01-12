<?php

namespace Modules\Crm\Http\Controllers;

use Inertia\Inertia;
use Modules\Core\Http\Controllers\Controller;
use Modules\Core\Traits\HasPermissionMiddleware;
use Modules\Crm\Http\Requests\CustomerType\StoreRequest;
use Modules\Crm\Http\Requests\CustomerType\UpdateRequest;
use Modules\Crm\Http\Resources\CustomerTypeResource;
use Modules\Crm\Services\CustomerTypeService;

class CustomerTypesController extends Controller
{
    use HasPermissionMiddleware;

    public function __construct(protected CustomerTypeService $customerTypeService)
    {
        $this->setupPermissionMiddleware();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->exists('dt_params')) {
            $params = json_decode(request('dt_params', '[]'), true);

            return response()->json($this->customerTypeService->list($params));
        }

        return Inertia::render('Crm::CustomerTypes/List', [
            'toast' => session('toast'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Crm::CustomerTypes/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $this->customerTypeService->create($data);

        return redirect()->route('customertypes.index')->with('toast', [
            'severity' => 'success',
            'summary' => __('generics.messages.saved_successfully'),
            'detail' => __('generics.messages.saved_successfully'),
            'life' => 5000,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $customertype = $this->customerTypeService->find($id);

        return Inertia::render('Crm::CustomerTypes/Show', [
            'data' => new CustomerTypeResource($customertype),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $customertype = $this->customerTypeService->find($id);

        return Inertia::render('Crm::CustomerTypes/Edit', [
            'data' => new CustomerTypeResource($customertype),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, int $id)
    {
        $data = $request->validated();
        $customertype = $this->customerTypeService->update($id, $data);

        return redirect()->route('customertypes.index')->with('toast', [
            'severity' => 'success',
            'summary' => __('generics.messages.saved_successfully'),
            'detail' => __('generics.messages.saved_successfully'),
            'life' => 5000,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $this->customerTypeService->delete($id);

        return response()->noContent();
    }
}
