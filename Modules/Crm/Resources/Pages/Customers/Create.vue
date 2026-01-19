<script setup>
import { useForm } from '@inertiajs/vue3';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import FormCustomer from '@Crm/Pages/Customers/Form.vue';

const props = defineProps({
  customer_types: Array,
  dni_locations: Array,
});

const form = useForm({
  dni: null,
  first_name: null,
  last_name: null,
  email: null,
  phone_e164: null,
  customer_type_id: null,
  dni_location_id: null,
  notes: null,
});

const submitHandler = () => form.post(route('customers.store'));
</script>

<template>
  <AuthenticatedLayout :title="__('customer.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('customer.titles.create')"
      :breadcrumbs="[{ to: 'customers.index', text: __('customer.titles.entity_breadcrumb') }, { text: __('generics.actions.create') }]"
      :form="{ instance: form, submitHandler, submitText: __('generics.buttons.create'), hrefCancel: route('customers.index') }"
    />
    <FormCustomer
      :form="form"
      :customer_types="customer_types"
      :dni_locations="dni_locations"
      :submitHandler="submitHandler"
    />
  </AuthenticatedLayout>
</template>
