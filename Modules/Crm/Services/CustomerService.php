<?php

namespace Modules\Crm\Services;

use Modules\Crm\Models\Customer;
use Modules\Core\Services\PrimevueDatatables;

class CustomerService
{
    protected const SEARCHABLE_COLUMNS = ['dni', 'full_name', 'email', 'phone_e164'];

    public function list(Array $params = [])
    {
        $query = Customer::query();

        $datatable = new PrimevueDatatables($params, self::SEARCHABLE_COLUMNS);
        $customers = $datatable->of($query)->make();

        return $customers;
    }

    public function find(int $id): Customer
    {
        return Customer::findOrFail($id);
    }

    public function create(Array $data): Customer
    {
        $customer = new Customer;
        $customer->dni = $data['dni'];
        $customer->first_name = $data['first_name'];
        $customer->last_name = $data['last_name'];

        $customer->full_name = trim($customer->first_name . ' ' . $customer->last_name);

        $customer->email = $data['email'];
        $customer->phone_e164 = $data['phone_e164'];
        $customer->customer_type_id = $data['customer_type_id'];
        $customer->dni_location_id = $data['dni_location_id'];
        $customer->notes = $data['notes'];
        $customer->save();

        return $customer;
    }

    public function update(int $id, Array $data): Customer
    {
        $customer = $this->find($id);
        $customer->dni = $data['dni'] ?? $customer->dni;
        $customer->first_name = $data['first_name'] ?? $customer->first_name;
        $customer->last_name = $data['last_name'] ?? $customer->last_name;

        $customer->full_name = trim($customer->first_name . ' ' . $customer->last_name);

        $customer->email = $data['email'] ?? $customer->email;
        $customer->phone_e164 = $data['phone_e164'] ?? $customer->phone_e164;
        $customer->customer_type_id = $data['customer_type_id'] ?? $customer->customer_type_id;
        $customer->dni_location_id = $data['dni_location_id'] ?? $customer->dni_location_id;
        $customer->notes = $data['notes'] ?? $customer->notes;
        $customer->save();

        return $customer;
    }

    public function delete(int $id): bool
    {
        $customer = $this->find($id);
        return $customer->delete();
    }
}
