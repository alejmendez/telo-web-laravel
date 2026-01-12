<?php

namespace Modules\Crm\Http\Controllers;

use Inertia\Inertia;
use Modules\Core\Http\Controllers\Controller;
use Modules\Core\Traits\HasPermissionMiddleware;
use Modules\Crm\Http\Requests\ContactType\StoreRequest;
use Modules\Crm\Http\Requests\ContactType\UpdateRequest;
use Modules\Crm\Http\Resources\ContactTypeResource;
use Modules\Crm\Services\ContactTypeService;

class ContactTypesController extends Controller
{
    use HasPermissionMiddleware;

    public function __construct(protected ContactTypeService $contactTypeService)
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

            return response()->json($this->contactTypeService->list($params));
        }

        return Inertia::render('Crm::ContactTypes/List', [
            'toast' => session('toast'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Crm::ContactTypes/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $this->contactTypeService->create($data);

        return redirect()->route('contacttypes.index')->with('toast', [
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
        $contacttype = $this->contactTypeService->find($id);

        return Inertia::render('Crm::ContactTypes/Show', [
            'data' => new ContactTypeResource($contacttype),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $contacttype = $this->contactTypeService->find($id);

        return Inertia::render('Crm::ContactTypes/Edit', [
            'data' => new ContactTypeResource($contacttype),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, int $id)
    {
        $data = $request->validated();
        $contacttype = $this->contactTypeService->update($id, $data);

        return redirect()->route('contacttypes.index')->with('toast', [
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
        $this->contactTypeService->delete($id);

        return response()->noContent();
    }
}
