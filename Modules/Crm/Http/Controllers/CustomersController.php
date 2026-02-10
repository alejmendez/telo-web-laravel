<?php

namespace Modules\Crm\Http\Controllers;

use Inertia\Inertia;
use Modules\Core\Http\Controllers\Controller;
use Modules\Core\Traits\HasPermissionMiddleware;
use Modules\Crm\Http\Requests\Customer\StoreRequest;
use Modules\Crm\Http\Requests\Customer\UpdateRequest;
use Modules\Crm\Http\Resources\CustomerResource;
use Modules\Crm\Http\Resources\CustomerResourceCollection;
use Modules\Crm\Services\ContactTypeService;
use Modules\Crm\Services\CustomerService;
use Modules\Crm\Services\CustomerTypeService;
use Modules\Crm\Services\LocationService;

class CustomersController extends Controller
{
    use HasPermissionMiddleware;

    public function __construct(
        protected CustomerService $customerService,
        protected CustomerTypeService $customerTypeService,
        protected LocationService $locationService,
        protected ContactTypeService $contactTypeService
    )
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

            $data = $this->customerService->list($params);
            return response()->json(new CustomerResourceCollection($data));
        }

        return Inertia::render('Crm::Customers/List', [
            'toast' => session('toast'),
            'customer_types' => $this->customerTypeService->listAsSelect(),
            'locations' => $this->locationService->list(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Crm::Customers/Create', [
            'customer_types' => $this->customerTypeService->listAsSelect(),
            'locations' => $this->locationService->list(),
            'contact_types' => $this->contactTypeService->listAsSelect(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $this->customerService->create($data);

        return redirect()->route('customers.index')->with('toast', [
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
        $customer = $this->customerService->find($id);

        return Inertia::render('Crm::Customers/Show', [
            'data' => new CustomerResource($customer),
            'customer_types' => $this->customerTypeService->listAsSelect(),
            'locations' => $this->locationService->list(),
            'contact_types' => $this->contactTypeService->listAsSelect(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $customer = $this->customerService->find($id);

        return Inertia::render('Crm::Customers/Edit', [
            'data' => new CustomerResource($customer),
            'customer_types' => $this->customerTypeService->listAsSelect(),
            'locations' => $this->locationService->list(),
            'contact_types' => $this->contactTypeService->listAsSelect(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, int $id)
    {
        $data = $request->validated();
        $customer = $this->customerService->update($id, $data);

        return redirect()->route('customers.index')->with('toast', [
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
        $this->customerService->delete($id);

        return response()->noContent();
    }
}
