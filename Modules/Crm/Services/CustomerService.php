<?php

namespace Modules\Crm\Services;

use Modules\Core\Services\PrimevueDatatables;
use Modules\Crm\Models\Customer;
use Modules\Crm\Models\CustomerAddress;
use Modules\Crm\Models\CustomerContact;

class CustomerService
{
    protected const SEARCHABLE_COLUMNS = ['dni', 'full_name'];

    public function list(array $params = [])
    {
        $query = Customer::query()->with(['customerType', 'addresses.location', 'contacts']);

        $datatable = new PrimevueDatatables($params, self::SEARCHABLE_COLUMNS);
        $customers = $datatable->of($query)->make();

        return $customers;
    }

    public function listAsSelect(array $filter = [])
    {
        $primaryAddressSub = CustomerAddress::query()
            ->join('locations', 'customer_addresses.location_id', '=', 'locations.id')
            ->whereColumn('customer_addresses.customer_id', 'customers.id')
            // ->where('customer_addresses.is_primary', true)
            ->selectRaw("locations.name || ' - ' || customer_addresses.address")
            ->limit(1);

        $query = Customer::select('customers.id', 'customers.full_name', 'customers.dni')
            ->selectSub($primaryAddressSub, 'primary_address')
            ->orderBy('customers.full_name');

        return query_to_select(
            $query,
            ['id', 'full_name', 'dni'],
            $filter
        )->map(function (Customer $customer) {
            return [
                'value' => $customer->id,
                'text' => $customer->full_name.' - '.$customer->dni,
                'address' => $customer->primary_address,
            ];
        });
    }

    public function find(int $id): Customer
    {
        return Customer::with('contacts')->findOrFail($id);
    }

    public function create(array $data): Customer
    {
        $customer = new Customer;
        $customer->dni = $data['dni'];
        $customer->first_name = $data['first_name'];
        $customer->last_name = $data['last_name'];
        $customer->full_name = trim($customer->first_name.' '.$customer->last_name);
        $customer->customer_type_id = $data['customer_type_id'];
        $customer->notes = $data['notes'];

        $customer->save();

        $this->create_contacts($customer, $data['contacts']);
        $this->create_addresses($customer, $data['addresses']);

        return $customer;
    }

    public function update(int $id, array $data): Customer
    {
        $customer = $this->find($id);
        $customer->dni = $data['dni'] ?? $customer->dni;
        $customer->first_name = $data['first_name'] ?? $customer->first_name;
        $customer->last_name = $data['last_name'] ?? $customer->last_name;
        $customer->full_name = trim($customer->first_name.' '.$customer->last_name);
        $customer->customer_type_id = $data['customer_type_id'] ?? $customer->customer_type_id;
        $customer->notes = $data['notes'] ?? $customer->notes;

        $customer->save();

        $this->update_contacts($customer, $data['contacts']);
        $this->update_addresses($customer, $data['addresses']);

        return $customer;
    }

    protected function create_contacts(Customer $customer, array $contacts_data)
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

    protected function update_contacts(Customer $customer, array $contacts_data)
    {
        $contacts = collect($contacts_data);

        $idContacts = $customer->contacts()->pluck('id');
        $idContactsToDestroy = $idContacts->filter(function ($id, int $key) use ($contacts) {
            return ! $contacts->firstWhere('id', $id);
        })->toArray();

        CustomerContact::destroy($idContactsToDestroy);
        foreach ($contacts as $contact) {
            if ($contact['contact_type'] == null && $contact['content'] == null) {
                continue;
            }
            $customer_contact = CustomerContact::find($contact['id']);
            if (! $customer_contact) {
                $customer_contact = new CustomerContact;
                $customer_contact->customer_id = $customer->id;
            }

            $customer_contact->contact_type = $contact['contact_type'];
            $customer_contact->content = $contact['content'];
            $customer_contact->save();
        }
    }

    protected function create_addresses(Customer $customer, array $addresses_data)
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

    protected function update_addresses(Customer $customer, array $addresses_data)
    {
        $addresses = collect($addresses_data);

        $idAddresses = $customer->addresses()->pluck('id');
        $idAddressesToDestroy = $idAddresses->filter(function ($id, int $key) use ($addresses) {
            return ! $addresses->firstWhere('id', $id);
        })->toArray();

        CustomerAddress::destroy($idAddressesToDestroy);
        foreach ($addresses as $address) {
            if ($address['location_id'] == null && $address['address'] == null && $address['postal_code'] == null) {
                continue;
            }
            $customer_address = CustomerAddress::find($address['id']);
            if (! $customer_address) {
                $customer_address = new CustomerAddress;
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
