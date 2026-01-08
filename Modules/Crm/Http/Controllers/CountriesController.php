<?php

namespace Modules\Crm\Http\Controllers;

use Inertia\Inertia;
use Modules\Core\Http\Controllers\Controller;
use Modules\Core\Traits\HasPermissionMiddleware;
use Modules\Crm\Http\Requests\StoreCountryRequest;
use Modules\Crm\Http\Requests\UpdateCountryRequest;
use Modules\Crm\Http\Resources\CountryResource;
use Modules\Crm\Services\CountryService;

class CountriesController extends Controller
{
    use HasPermissionMiddleware;

    public function __construct(protected CountryService $countryService)
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

            return response()->json($this->countryService->list($params));
        }

        return Inertia::render('Crm::Countries/List', [
            'toast' => session('toast'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Crm::Countries/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCountryRequest $request)
    {
        $data = $request->validated();
        $this->countryService->create($data);

        return redirect()->route('countries.index')->with('toast', [
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
        $country = $this->countryService->find($id);

        return Inertia::render('Crm::Countries/Show', [
            'data' => new CountryResource($country),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $country = $this->countryService->find($id);

        return Inertia::render('Crm::Countries/Edit', [
            'data' => new CountryResource($country),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCountryRequest $request, int $id)
    {
        $data = $request->validated();
        $country = $this->countryService->update($id, $data);

        return redirect()->route('countries.index')->with('toast', [
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
        $this->countryService->delete($id);

        return response()->noContent();
    }
}
