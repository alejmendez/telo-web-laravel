<?php

namespace Modules\Crm\Http\Controllers;

use Inertia\Inertia;
use Modules\Core\Http\Controllers\Controller;
use Modules\Core\Traits\HasPermissionMiddleware;
use Modules\Crm\Http\Requests\Location\StoreRequest;
use Modules\Crm\Http\Requests\Location\UpdateRequest;
use Modules\Crm\Http\Resources\LocationResource;
use Modules\Crm\Services\LocationService;

class LocationsController extends Controller
{
    use HasPermissionMiddleware;

    public function __construct(protected LocationService $locationService)
    {
        $this->setupPermissionMiddleware();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Crm::Locations/List', [
            'toast' => session('toast'),
            'nodes' => $this->locationService->list(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Crm::Locations/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $this->locationService->create($data);

        return redirect()->route('locations.index')->with('toast', [
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
        $location = $this->locationService->find($id);

        return Inertia::render('Crm::Locations/Show', [
            'data' => new LocationResource($location),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $location = $this->locationService->find($id);

        return Inertia::render('Crm::Locations/Edit', [
            'data' => new LocationResource($location),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, int $id)
    {
        $data = $request->validated();
        $location = $this->locationService->update($id, $data);

        return redirect()->route('locations.index')->with('toast', [
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
        $this->locationService->delete($id);

        return response()->noContent();
    }
}
