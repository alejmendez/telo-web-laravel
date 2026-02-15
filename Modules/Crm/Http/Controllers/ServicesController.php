<?php

namespace Modules\Crm\Http\Controllers;

use Inertia\Inertia;
use Modules\Core\Http\Controllers\Controller;
use Modules\Core\Traits\HasPermissionMiddleware;
use Modules\Crm\Http\Requests\Services\StoreRequest;
use Modules\Crm\Http\Requests\Services\UpdateRequest;
use Modules\Crm\Http\Resources\ServicesResource;
use Modules\Crm\Services\CustomerService;
use Modules\Crm\Services\ProfessionalService;
use Modules\Crm\Services\RequestsService;
use Modules\Crm\Services\ServicesService;
use Modules\Crm\Services\StatusService;
use Modules\Crm\Http\Resources\ServicesResourceCollection;

class ServicesController extends Controller
{
    use HasPermissionMiddleware;

    public function __construct(
        protected RequestsService $requestsService,
        protected ServicesService $servicesService,
        protected CustomerService $customerService,
        protected ProfessionalService $professionalService,
        protected StatusService $statusService,
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

            return $this->servicesService->list($params);
        }

        return Inertia::render('Crm::Services/List', [
            'toast' => session('toast'),
            'statuses' => $this->statusService->listAsSelect(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Crm::Services/Create', $this->view_data());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $this->servicesService->create($data);

        return redirect()->route('services.index')->with('toast', [
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
        $services = $this->servicesService->find($id);

        return Inertia::render('Crm::Services/Show', $this->view_data($services));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $services = $this->servicesService->find($id);

        return Inertia::render('Crm::Services/Edit', $this->view_data($services));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, int $id)
    {
        $data = $request->validated();
        $services = $this->servicesService->update($id, $data);

        return redirect()->route('services.index')->with('toast', [
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
        $this->servicesService->delete($id);

        return response()->noContent();
    }

    /**
     * Get view data.
     */
    protected function view_data($services = null): array
    {
        $data = [
            'requests' => $this->requestsService->listAsSelect(),
            'professionals' => $this->professionalService->listAsSelect(),
            'statuses' => $this->statusService->listAsSelect(),
        ];

        if ($services) {
            $data['data'] = new ServicesResource($services);
        }

        return $data;
    }
}
