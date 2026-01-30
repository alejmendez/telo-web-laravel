<?php

namespace Modules\Crm\Services;

use Modules\Crm\Models\Professional;
use Modules\Core\Services\PrimevueDatatables;

class ProfessionalService
{
    protected const SEARCHABLE_COLUMNS = ['dni', 'full_name', 'email', 'phone_e164'];

    public function list(Array $params = [])
    {
        $query = Professional::query()->with(['professionalType', 'location']);

        $datatable = new PrimevueDatatables($params, self::SEARCHABLE_COLUMNS);
        $professionals = $datatable->of($query)->make();

        return $professionals;
    }

    public function listAsSelect(array $filter = [])
    {
        return query_to_select(
            Professional::select('id', 'full_name', 'dni')->orderBy('full_name'),
            ['id', 'full_name', 'dni'],
            $filter
        )->map(function (Professional $professional) {
            return [
                'value' => $professional->id,
                'text' => $professional->full_name . ' - ' . $professional->dni,
            ];
        });
    }

    public function find(int $id): Professional
    {
        return Professional::with(['professionalType', 'location'])->findOrFail($id);
    }

    public function create(Array $data): Professional
    {
        $professional = new Professional;
        $professional->professional_type_id = $data['professional_type_id'];
        $professional->first_name = $data['first_name'];
        $professional->last_name = $data['last_name'];

        $professional->full_name = trim($professional->first_name . ' ' . $professional->last_name);

        $professional->email = $data['email'];
        $professional->phone_e164 = $data['phone_e164'];
        $professional->location_id = $data['location_id'];
        $professional->dni = $data['dni'];
        $professional->bio = $data['bio'] ?? null;
        $professional->save();

        return $professional;
    }

    public function update(int $id, Array $data): Professional
    {
        $professional = $this->find($id);
        $professional->professional_type_id = $data['professional_type_id'] ?? $professional->professional_type_id;
        $professional->first_name = $data['first_name'] ?? $professional->first_name;
        $professional->last_name = $data['last_name'] ?? $professional->last_name;

        $professional->full_name = trim($professional->first_name . ' ' . $professional->last_name);

        $professional->email = $data['email'] ?? $professional->email;
        $professional->phone_e164 = $data['phone_e164'] ?? $professional->phone_e164;
        $professional->location_id = $data['location_id'] ?? $professional->location_id;
        $professional->dni = $data['dni'] ?? $professional->dni;
        $professional->bio = $data['bio'] ?? $professional->bio;
        $professional->save();

        return $professional;
    }

    public function delete(int $id): bool
    {
        $professional = $this->find($id);
        return $professional->delete();
    }
}
