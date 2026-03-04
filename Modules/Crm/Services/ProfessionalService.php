<?php

namespace Modules\Crm\Services;

use Modules\Core\Services\PrimevueDatatables;
use Modules\Crm\Models\Professional;
use Modules\Crm\Models\ProfessionalAddress;
use Modules\Crm\Models\ProfessionalContact;

class ProfessionalService
{
    protected const SEARCHABLE_COLUMNS = ['dni', 'full_name'];

    public function list(array $params = [])
    {
        $query = Professional::query()->with(['professionalType', 'categories', 'addresses.location', 'contacts']);

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
                'text' => $professional->full_name.' - '.$professional->dni,
            ];
        });
    }

    public function find(int $id): Professional
    {
        return Professional::with(['professionalType', 'location', 'categories', 'addresses.location', 'contacts'])->findOrFail($id);
    }

    public function create(array $data): Professional
    {
        $professional = new Professional;
        $professional->dni = $data['dni'];
        $professional->professional_type_id = $data['professional_type_id'];
        $professional->first_name = $data['first_name'];
        $professional->last_name = $data['last_name'];
        $professional->full_name = trim($professional->first_name.' '.$professional->last_name);
        $professional->bio = $data['bio'] ?? null;
        $professional->save();

        if ($data['categories']) {
            $professional->categories()->sync($data['categories']);
        }

        $this->create_contacts($professional, $data['contacts']);
        $this->create_addresses($professional, $data['addresses']);

        return $professional;
    }

    public function update(int $id, array $data): Professional
    {
        $professional = $this->find($id);
        $professional->dni = $data['dni'] ?? $professional->dni;
        $professional->first_name = $data['first_name'] ?? $professional->first_name;
        $professional->last_name = $data['last_name'] ?? $professional->last_name;
        $professional->full_name = trim($professional->first_name.' '.$professional->last_name);
        $professional->professional_type_id = $data['professional_type_id'] ?? $professional->professional_type_id;
        $professional->bio = $data['bio'] ?? $professional->bio;
        $professional->save();

        if ($data['categories']) {
            $professional->categories()->sync($data['categories']);
        }

        $this->update_contacts($professional, $data['contacts']);
        $this->update_addresses($professional, $data['addresses']);

        return $professional;
    }

    protected function create_contacts(Professional $professional, array $contacts_data)
    {
        foreach ($contacts_data as $contact) {
            if ($contact['contact_type'] == null && $contact['content'] == null) {
                continue;
            }
            $professional_contact = new ProfessionalContact;
            $professional_contact->contact_type = $contact['contact_type'];
            $professional_contact->content = $contact['content'];
            $professional_contact->professional_id = $professional->id;
            $professional_contact->save();
        }
    }

    protected function update_contacts(Professional $professional, array $contacts_data)
    {
        $contacts = collect($contacts_data);

        $existingIds = $professional->contacts()->pluck('id');
        $incomingIds = $contacts->pluck('id')->filter();

        $toDestroy = $existingIds->diff($incomingIds)->toArray();
        ProfessionalContact::destroy($toDestroy);

        $existingContacts = ProfessionalContact::whereIn('id', $incomingIds)->get()->keyBy('id');

        foreach ($contacts as $contact) {
            if ($contact['contact_type'] == null && $contact['content'] == null) {
                continue;
            }
            $professional_contact = $existingContacts->get($contact['id']) ?? new ProfessionalContact(['professional_id' => $professional->id]);
            $professional_contact->contact_type = $contact['contact_type'];
            $professional_contact->content = $contact['content'];
            $professional_contact->save();
        }
    }

    protected function create_addresses(Professional $professional, array $addresses_data)
    {
        foreach ($addresses_data as $address) {
            if ($address['location_id'] == null && $address['address'] == null && $address['postal_code'] == null) {
                continue;
            }
            $professional_address = new ProfessionalAddress;
            $professional_address->location_id = $address['location_id'];
            $professional_address->address = $address['address'];
            $professional_address->postal_code = $address['postal_code'];
            $professional_address->is_primary = $address['is_primary'];
            $professional_address->professional_id = $professional->id;
            $professional_address->save();
        }
    }

    protected function update_addresses(Professional $professional, array $addresses_data)
    {
        $addresses = collect($addresses_data);

        $existingIds = $professional->addresses()->pluck('id');
        $incomingIds = $addresses->pluck('id')->filter();

        $toDestroy = $existingIds->diff($incomingIds)->toArray();
        ProfessionalAddress::destroy($toDestroy);

        $existingAddresses = ProfessionalAddress::whereIn('id', $incomingIds)->get()->keyBy('id');

        foreach ($addresses as $address) {
            if ($address['location_id'] == null && $address['address'] == null && $address['postal_code'] == null) {
                continue;
            }
            $professional_address = $existingAddresses->get($address['id']) ?? new ProfessionalAddress(['professional_id' => $professional->id]);
            $professional_address->location_id = $address['location_id'];
            $professional_address->address = $address['address'];
            $professional_address->postal_code = $address['postal_code'];
            $professional_address->is_primary = $address['is_primary'];
            $professional_address->save();
        }
    }

    public function delete(int $id): bool
    {
        $professional = $this->find($id);

        return $professional->delete();
    }
}
