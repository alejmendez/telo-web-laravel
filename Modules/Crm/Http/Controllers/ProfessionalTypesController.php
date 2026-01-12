<?php

namespace Modules\Crm\Http\Controllers;

use Inertia\Inertia;
use Modules\Core\Http\Controllers\Controller;
use Modules\Core\Traits\HasPermissionMiddleware;
use Modules\Crm\Http\Requests\ProfessionalType\StoreRequest;
use Modules\Crm\Http\Requests\ProfessionalType\UpdateRequest;
use Modules\Crm\Http\Resources\ProfessionalTypeResource;
use Modules\Crm\Services\ProfessionalTypeService;

class ProfessionalTypesController extends Controller
{
    use HasPermissionMiddleware;

    public function __construct(protected ProfessionalTypeService $professionalTypeService)
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

            return response()->json($this->professionalTypeService->list($params));
        }

        return Inertia::render('Crm::ProfessionalTypes/List', [
            'toast' => session('toast'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Crm::ProfessionalTypes/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $this->professionalTypeService->create($data);

        return redirect()->route('professionaltypes.index')->with('toast', [
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
        $professionaltype = $this->professionalTypeService->find($id);

        return Inertia::render('Crm::ProfessionalTypes/Show', [
            'data' => new ProfessionalTypeResource($professionaltype),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $professionaltype = $this->professionalTypeService->find($id);

        return Inertia::render('Crm::ProfessionalTypes/Edit', [
            'data' => new ProfessionalTypeResource($professionaltype),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, int $id)
    {
        $data = $request->validated();
        $professionaltype = $this->professionalTypeService->update($id, $data);

        return redirect()->route('professionaltypes.index')->with('toast', [
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
        $this->professionalTypeService->delete($id);

        return response()->noContent();
    }
}
