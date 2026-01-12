<?php

namespace Modules\Crm\Http\Controllers;

use Inertia\Inertia;
use Modules\Core\Http\Controllers\Controller;
use Modules\Core\Traits\HasPermissionMiddleware;
use Modules\Crm\Http\Requests\UrgencyType\StoreRequest;
use Modules\Crm\Http\Requests\UrgencyType\UpdateRequest;
use Modules\Crm\Http\Resources\UrgencyTypeResource;
use Modules\Crm\Services\UrgencyTypeService;

class UrgencyTypesController extends Controller
{
    use HasPermissionMiddleware;

    public function __construct(protected UrgencyTypeService $urgencyTypeService)
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

            return response()->json($this->urgencyTypeService->list($params));
        }

        return Inertia::render('Crm::UrgencyTypes/List', [
            'toast' => session('toast'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Crm::UrgencyTypes/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $this->urgencyTypeService->create($data);

        return redirect()->route('urgencytypes.index')->with('toast', [
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
        $urgencytype = $this->urgencyTypeService->find($id);

        return Inertia::render('Crm::UrgencyTypes/Show', [
            'data' => new UrgencyTypeResource($urgencytype),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $urgencytype = $this->urgencyTypeService->find($id);

        return Inertia::render('Crm::UrgencyTypes/Edit', [
            'data' => new UrgencyTypeResource($urgencytype),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, int $id)
    {
        $data = $request->validated();
        $urgencytype = $this->urgencyTypeService->update($id, $data);

        return redirect()->route('urgencytypes.index')->with('toast', [
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
        $this->urgencyTypeService->delete($id);

        return response()->noContent();
    }
}
