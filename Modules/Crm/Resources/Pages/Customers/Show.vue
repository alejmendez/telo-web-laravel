<script setup>
import { useForm } from '@inertiajs/vue3';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import FormCustomer from '@Crm/Pages/Customers/Form.vue';

const props = defineProps({
  data: Object,
});

const { data } = props.data;

const form = useForm({
  id: data.id,
  name: data.name,
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
      :readOnly="true"
      :submitHandler="submitHandler"
    />
  </AuthenticatedLayout>
</template>
