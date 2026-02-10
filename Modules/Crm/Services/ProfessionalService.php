<?php

namespace Modules\Crm\Services;

use Modules\Crm\Models\Professional;
use Modules\Crm\Models\ProfessionalAddress;
use Modules\Crm\Models\ProfessionalContact;
use Modules\Core\Services\PrimevueDatatables;

class ProfessionalService
{
    protected const SEARCHABLE_COLUMNS = ['dni', 'full_name', 'email', 'phone_e164'];

    public function list(Array $params = [])
    {
        $query = Professional::query()->with(['professionalType', 'categories', 'addresses', 'contacts']);

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
        $professional->dni = $data['dni'];
        $professional->professional_type_id = $data['professional_type_id'];
        $professional->first_name = $data['first_name'];
        $professional->last_name = $data['last_name'];
        $professional->full_name = trim($professional->first_name . ' ' . $professional->last_name);
        $professional->bio = $data['bio'] ?? null;
        $professional->categories()->sync($data['categories'] ?? []);
        $professional->save();

        $this->create_contacts($professional, $data['contacts']);
        $this->create_addresses($professional, $data['addresses']);

        return $professional;
    }

    public function update(int $id, Array $data): Professional
    {
        $professional = $this->find($id);
        $professional->dni = $data['dni'] ?? $professional->dni;
        $professional->first_name = $data['first_name'] ?? $professional->first_name;
        $professional->last_name = $data['last_name'] ?? $professional->last_name;
        $professional->full_name = trim($professional->first_name . ' ' . $professional->last_name);
        $professional->professional_type_id = $data['professional_type_id'] ?? $professional->professional_type_id;
        $professional->bio = $data['bio'] ?? $professional->bio;
        $professional->categories()->sync($data['categories'] ?? []);
        $professional->save();

        $this->update_contacts($professional, $data['contacts']);
        $this->update_addresses($professional, $data['addresses']);

        return $professional;
    }

    protected function create_contacts(Professional $professional, Array $contacts_data)
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

    protected function update_contacts(Professional $professional, Array $contacts_data)
    {
        $contacts = collect($contacts_data);

        $idContacts = $professional->contacts()->pluck('id');
        $idContactsToDestroy = $idContacts->filter(function ($id, int $key) use ($contacts) {
            return !$contacts->firstWhere('id', $id);
        })->toArray();

        ProfessionalContact::destroy($idContactsToDestroy);
        foreach ($contacts as $contact) {
            if ($contact['contact_type'] == null && $contact['content'] == null) {
                continue;
            }
            $professional_contact = ProfessionalContact::find($contact['id']);
            if (!$professional_contact) {
                $professional_contact = new ProfessionalContact();
                $professional_contact->professional_id = $professional->id;
            }

            $professional_contact->contact_type = $contact['contact_type'];
            $professional_contact->content = $contact['content'];
            $professional_contact->save();
        }
    }

    protected function create_addresses(Professional $professional, Array $addresses_data)
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

    protected function update_addresses(Professional $professional, Array $addresses_data)
    {
        $addresses = collect($addresses_data);

        $idAddresses = $professional->addresses()->pluck('id');
        $idAddressesToDestroy = $idAddresses->filter(function ($id, int $key) use ($addresses) {
            return !$addresses->firstWhere('id', $id);
        })->toArray();

        ProfessionalAddress::destroy($idAddressesToDestroy);
        foreach ($addresses as $address) {
            if ($address['location_id'] == null && $address['address'] == null && $address['postal_code'] == null) {
                continue;
            }
            $professional_address = ProfessionalAddress::find($address['id']);
            if (!$professional_address) {
                $professional_address = new ProfessionalAddress();
                $professional_address->professional_id = $professional->id;
            }

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
