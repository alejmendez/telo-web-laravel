<?php

namespace Modules\Crm\Http\Controllers;

use Inertia\Inertia;
use Modules\Core\Http\Controllers\Controller;
use Modules\Core\Traits\HasPermissionMiddleware;
use Modules\Crm\Http\Requests\Professional\StoreRequest;
use Modules\Crm\Http\Requests\Professional\UpdateRequest;
use Modules\Crm\Http\Resources\ProfessionalResource;
use Modules\Crm\Services\ProfessionalService;
use Modules\Crm\Services\LocationService;
use Modules\Crm\Services\ProfessionalTypeService;

class ProfessionalsController extends Controller
{
    use HasPermissionMiddleware;

    public function __construct(
        protected ProfessionalService $professionalService,
        protected ProfessionalTypeService $professionalTypeService,
        protected LocationService $locationService
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

            return response()->json($this->professionalService->list($params));
        }

        return Inertia::render('Crm::Professionals/List', [
            'toast' => session('toast'),
            'professional_types' => $this->professionalTypeService->listAsSelect(),
            'locations' => $this->locationService->list(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Crm::Professionals/Create', [
            'professional_types' => $this->professionalTypeService->listAsSelect(),
            'locations' => $this->locationService->list(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $this->professionalService->create($data);

        return redirect()->route('professionals.index')->with('toast', [
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
        $professional = $this->professionalService->find($id);

        return Inertia::render('Crm::Professionals/Show', [
            'data' => new ProfessionalResource($professional),
            'professional_types' => $this->professionalTypeService->listAsSelect(),
            'locations' => $this->locationService->list(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $professional = $this->professionalService->find($id);

        return Inertia::render('Crm::Professionals/Edit', [
            'data' => new ProfessionalResource($professional),
            'professional_types' => $this->professionalTypeService->listAsSelect(),
            'locations' => $this->locationService->list(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, int $id)
    {
        $data = $request->validated();
        $professional = $this->professionalService->update($id, $data);

        return redirect()->route('professionals.index')->with('toast', [
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
        $this->professionalService->delete($id);

        return response()->noContent();
    }
}
