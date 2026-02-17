<?php

namespace Modules\Crm\Http\Controllers;

use Inertia\Inertia;
use Modules\Core\Http\Controllers\Controller;
use Modules\Core\Traits\HasPermissionMiddleware;
use Modules\Crm\Http\Requests\Requests\StoreRequest;
use Modules\Crm\Http\Requests\Requests\UpdateRequest;
use Modules\Crm\Http\Resources\RequestsResource;
use Modules\Crm\Services\CategoryService;
use Modules\Crm\Services\CustomerService;
use Modules\Crm\Services\ProfessionalService;
use Modules\Crm\Services\RequestsService;
use Modules\Crm\Services\StatusService;
use Modules\Crm\Services\UrgencyTypeService;

class RequestsController extends Controller
{
    use HasPermissionMiddleware;

    public function __construct(
        protected RequestsService $requestsService,
        protected CustomerService $customerService,
        protected CategoryService $categoryService,
        protected UrgencyTypeService $urgencyTypeService,
        protected StatusService $statusService,
        protected ProfessionalService $professionalService
    ) {
        $this->setupPermissionMiddleware();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->exists('dt_params')) {
            $params = json_decode(request('dt_params', '[]'), true);

            return response()->json($this->requestsService->list($params));
        }

        return Inertia::render('Crm::Requests/List', [
            'toast' => session('toast'),
            'categories' => $this->categoryService->listAsSelect(),
            'urgency_types' => $this->urgencyTypeService->listAsSelect(),
            'customers' => $this->customerService->listAsSelect(),
            'professionals' => $this->professionalService->listAsSelect(),
            'statuses' => $this->statusService->listAsSelect(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Crm::Requests/Create', [
            'categories' => $this->categoryService->listAsSelect(),
            'urgency_types' => $this->urgencyTypeService->listAsSelect(),
            'customers' => $this->customerService->listAsSelect(),
            'professionals' => $this->professionalService->listAsSelect(),
            'statuses' => $this->statusService->listAsSelect(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $this->requestsService->create($data);

        return redirect()->route('requests.index')->with('toast', [
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
        $requests = $this->requestsService->find($id);

        return Inertia::render('Crm::Requests/Show', [
            'data' => new RequestsResource($requests),
            'histories' => $requests->histories,
            'categories' => $this->categoryService->listAsSelect(),
            'urgency_types' => $this->urgencyTypeService->listAsSelect(),
            'customers' => $this->customerService->listAsSelect(),
            'professionals' => $this->professionalService->listAsSelect(),
            'statuses' => $this->statusService->listAsSelect(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $requests = $this->requestsService->find($id);

        return Inertia::render('Crm::Requests/Edit', [
            'data' => new RequestsResource($requests),
            'histories' => $requests->histories,
            'categories' => $this->categoryService->listAsSelect(),
            'urgency_types' => $this->urgencyTypeService->listAsSelect(),
            'customers' => $this->customerService->listAsSelect(),
            'professionals' => $this->professionalService->listAsSelect(),
            'statuses' => $this->statusService->listAsSelect(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, int $id)
    {
        $data = $request->validated();
        $requests = $this->requestsService->update($id, $data);

        return redirect()->route('requests.index')->with('toast', [
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
        $this->requestsService->delete($id);

        return response()->noContent();
    }
}
