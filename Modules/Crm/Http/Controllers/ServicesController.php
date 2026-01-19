<?php

namespace Modules\Crm\Http\Controllers;

use Inertia\Inertia;
use Modules\Core\Http\Controllers\Controller;
use Modules\Core\Traits\HasPermissionMiddleware;
use Modules\Crm\Http\Requests\Services\StoreRequest;
use Modules\Crm\Http\Requests\Services\UpdateRequest;
use Modules\Crm\Http\Resources\ServicesResource;
use Modules\Crm\Services\ServicesService;

class ServicesController extends Controller
{
    use HasPermissionMiddleware;

    public function __construct(protected ServicesService $servicesService)
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

            return response()->json($this->servicesService->list($params));
        }

        return Inertia::render('Crm::Services/List', [
            'toast' => session('toast'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Crm::Services/Create');
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

        return Inertia::render('Crm::Services/Show', [
            'data' => new ServicesResource($services),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $services = $this->servicesService->find($id);

        return Inertia::render('Crm::Services/Edit', [
            'data' => new ServicesResource($services),
        ]);
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
}
