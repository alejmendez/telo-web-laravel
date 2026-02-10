<?php

namespace Modules\Crm\Services;

use Modules\Crm\Models\Customer;
use Modules\Crm\Models\CustomerContact;
use Modules\Crm\Models\CustomerAddress;
use Modules\Core\Services\PrimevueDatatables;

class CustomerService
{
    protected const SEARCHABLE_COLUMNS = ['dni', 'full_name'];

    public function list(Array $params = [])
    {
        $query = Customer::query()->with(['customerType', 'addresses', 'contacts']);

        $datatable = new PrimevueDatatables($params, self::SEARCHABLE_COLUMNS);
        $customers = $datatable->of($query)->make();

        return $customers;
    }

    public function listAsSelect(array $filter = [])
    {
        return query_to_select(
            Customer::select('id', 'full_name', 'dni')->orderBy('full_name'),
            ['id', 'full_name', 'dni'],
            $filter
        )->map(function (Customer $customer) {
            return [
                'value' => $customer->id,
                'text' => $customer->full_name . ' - ' . $customer->dni,
            ];
        });
    }

    public function find(int $id): Customer
    {
        return Customer::with('contacts')->findOrFail($id);
    }

    public function create(Array $data): Customer
    {
        $customer = new Customer;
        $customer->dni = $data['dni'];
        $customer->first_name = $data['first_name'];
        $customer->last_name = $data['last_name'];
        $customer->full_name = trim($customer->first_name . ' ' . $customer->last_name);
        $customer->customer_type_id = $data['customer_type_id'];
        $customer->notes = $data['notes'];

        $customer->save();

        $this->create_contacts($customer, $data['contacts']);
        $this->create_addresses($customer, $data['addresses']);

        return $customer;
    }

    public function update(int $id, Array $data): Customer
    {
        $customer = $this->find($id);
        $customer->dni = $data['dni'] ?? $customer->dni;
        $customer->first_name = $data['first_name'] ?? $customer->first_name;
        $customer->last_name = $data['last_name'] ?? $customer->last_name;
        $customer->full_name = trim($customer->first_name . ' ' . $customer->last_name);
        $customer->customer_type_id = $data['customer_type_id'] ?? $customer->customer_type_id;
        $customer->notes = $data['notes'] ?? $customer->notes;

        $customer->save();

        $this->update_contacts($customer, $data['contacts']);
        $this->update_addresses($customer, $data['addresses']);

        return $customer;
    }

    protected function create_contacts(Customer $customer, Array $contacts_data)
    {
        foreach ($contacts_data as $contact) {
            if ($contact['contact_type'] == null && $contact['content'] == null) {
                continue;
            }
            $customer_contact = new CustomerContact;
            $customer_contact->contact_type = $contact['contact_type'];
            $customer_contact->content = $contact['content'];
            $customer_contact->customer_id = $customer->id;
            $customer_contact->save();
        }
    }

    protected function update_contacts(Customer $customer, Array $contacts_data)
    {
        $contacts = collect($contacts_data);

        $idContacts = $customer->contacts()->pluck('id');
        $idContactsToDestroy = $idContacts->filter(function ($id, int $key) use ($contacts) {
            return !$contacts->firstWhere('id', $id);
        })->toArray();

        CustomerContact::destroy($idContactsToDestroy);
        foreach ($contacts as $contact) {
            if ($contact['contact_type'] == null && $contact['content'] == null) {
                continue;
            }
            $customer_contact = CustomerContact::find($contact['id']);
            if (!$customer_contact) {
                $customer_contact = new CustomerContact();
                $customer_contact->customer_id = $customer->id;
            }

            $customer_contact->contact_type = $contact['contact_type'];
            $customer_contact->content = $contact['content'];
            $customer_contact->save();
        }
    }

    protected function create_addresses(Customer $customer, Array $addresses_data)
    {
        foreach ($addresses_data as $address) {
            if ($address['location_id'] == null && $address['address'] == null && $address['postal_code'] == null) {
                continue;
            }
            $customer_address = new CustomerAddress;
            $customer_address->location_id = $address['location_id'];
            $customer_address->address = $address['address'];
            $customer_address->postal_code = $address['postal_code'];
            $customer_address->is_primary = $address['is_primary'];
            $customer_address->customer_id = $customer->id;
            $customer_address->save();
        }
    }

    protected function update_addresses(Customer $customer, Array $addresses_data)
    {
        $addresses = collect($addresses_data);

        $idAddresses = $customer->addresses()->pluck('id');
        $idAddressesToDestroy = $idAddresses->filter(function ($id, int $key) use ($addresses) {
            return !$addresses->firstWhere('id', $id);
        })->toArray();

        CustomerAddress::destroy($idAddressesToDestroy);
        foreach ($addresses as $address) {
            if ($address['location_id'] == null && $address['address'] == null && $address['postal_code'] == null) {
                continue;
            }
            $customer_address = CustomerAddress::find($address['id']);
            if (!$customer_address) {
                $customer_address = new CustomerAddress();
                $customer_address->customer_id = $customer->id;
            }

            $customer_address->location_id = $address['location_id'];
            $customer_address->address = $address['address'];
            $customer_address->postal_code = $address['postal_code'];
            $customer_address->is_primary = $address['is_primary'];
            $customer_address->save();
        }
    }

    public function delete(int $id): bool
    {
        $customer = $this->find($id);
        return $customer->delete();
    }
}
