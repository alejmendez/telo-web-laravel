<script setup>
import { useForm } from '@inertiajs/vue3';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import FormCustomer from '@Crm/Pages/Customers/Form.vue';

const props = defineProps({
  data: Object,
  customer_types: Array,
  locations: Array,
});

const { data } = props.data;

const selectValueFromArray = (array, value) => {
  return array.find(item => item.value === value);
}

const form = useForm({
  _method: 'PATCH',
  id: data.id,
  dni: data.dni,
  first_name: data.first_name,
  last_name: data.last_name,
  email: data.email,
  phone_e164: data.phone_e164,
  customer_type_id: selectValueFromArray(props.customer_types, data.customer_type_id),
  location_id: {
    [data.location_id]: true
  },
  notes: data.notes,
});

const submitHandler = () => form.post(route('customers.update', data.id));
</script>

<template>
  <AuthenticatedLayout :title="__('customer.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('customer.titles.edit')"
      :breadcrumbs="[{ to: 'customers.index', text: __('customer.titles.entity_breadcrumb') }, { text: __('generics.actions.edit') }]"
      :form="{ instance: form, submitHandler, submitText: __('generics.buttons.save_edit'), hrefCancel: route('customers.index') }"
    />
    <FormCustomer
      :form="form"
      :customer_types="customer_types"
      :locations="locations"
      :submitHandler="submitHandler"
    />
  </AuthenticatedLayout>
</template>
