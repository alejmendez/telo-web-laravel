<script setup>
import { useForm } from '@inertiajs/vue3';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import FormCustomer from '@Crm/Pages/Customers/Form.vue';

const props = defineProps({
  data: Object,
  customer_types: Array,
  locations: Array,
  contact_types: Array,
});

const { data } = props.data;

const selectValueFromArray = (array, value) => {
  return array.find(item => item.value === value);
}

const contacts = data.contacts?.length
  ? data.contacts.map(({ id, contact_type, content }) => ({
      id: id,
      contact_type: selectValueFromArray(props.contact_types, contact_type),
      content,
    }))
  : [{ id: null, contact_type: null, content: null }];

const addresses = data.addresses?.length
  ? data.addresses.map(({ id, location_id, address, postal_code, is_primary }) => ({
      id: id,
      location_id: {
        [location_id]: true
      },
      address,
      postal_code,
      is_primary,
    }))
  : [{ id: null, location_id: null, address: null, postal_code: null, is_primary: true }];

const form = useForm({
  _method: 'PATCH',
  id: data.id,
  dni: data.dni,
  first_name: data.first_name,
  last_name: data.last_name,
  customer_type_id: selectValueFromArray(props.customer_types, data.customer_type_id),
  notes: data.notes,
  contacts: contacts,
  addresses: addresses,
});

const submitHandler = () => {};
</script>

<template>
  <AuthenticatedLayout :title="__('customer.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('customer.titles.show')"
      :breadcrumbs="[{ to: 'customers.index', text: __('customer.titles.entity_breadcrumb') }, { text: __('generics.actions.show') }]"
      :links="[{ to: route('customers.index'), text: __('generics.buttons.back') }]"
    />
    <FormCustomer
      :form="form"
      :customer_types="customer_types"
      :locations="locations"
      :contact_types="contact_types"
      :readOnly="true"
      :submitHandler="submitHandler"
    />
  </AuthenticatedLayout>
</template>
