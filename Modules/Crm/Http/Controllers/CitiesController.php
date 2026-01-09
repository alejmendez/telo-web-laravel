<?php

namespace Modules\Crm\Http\Controllers;

use Inertia\Inertia;
use Modules\Core\Http\Controllers\Controller;
use Modules\Core\Traits\HasPermissionMiddleware;
use Modules\Crm\Http\Requests\StoreCityRequest;
use Modules\Crm\Http\Requests\UpdateCityRequest;
use Modules\Crm\Http\Resources\CityResource;
use Modules\Crm\Services\CityService;
use Modules\Crm\Services\CountryService;

class CitiesController extends Controller
{
    use HasPermissionMiddleware;

    public function __construct(
        protected CountryService $countryService,
        protected CityService $cityService
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

            return response()->json($this->cityService->list($params));
        }

        return Inertia::render('Crm::Cities/List', [
            'toast' => session('toast'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Crm::Cities/Create', [
            'countries' => $this->countryService->list_for_select(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCityRequest $request)
    {
        $data = $request->validated();
        $this->cityService->create($data);

        return redirect()->route('cities.index')->with('toast', [
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
        $city = $this->cityService->find($id);

        return Inertia::render('Crm::Cities/Show', [
            'data' => new CityResource($city),
            'countries' => $this->countryService->list_for_select(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $city = $this->cityService->find($id);

        return Inertia::render('Crm::Cities/Edit', [
            'data' => new CityResource($city),
            'countries' => $this->countryService->list_for_select(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCityRequest $request, int $id)
    {
        $data = $request->validated();
        $city = $this->cityService->update($id, $data);

        return redirect()->route('cities.index')->with('toast', [
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
        $this->cityService->delete($id);

        return response()->noContent();
    }
}
