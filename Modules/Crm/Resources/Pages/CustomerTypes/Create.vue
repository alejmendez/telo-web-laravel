<script setup>
import { useForm } from '@inertiajs/vue3';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import FormCustomerType from '@Crm/Pages/CustomerTypes/Form.vue';

const props = defineProps();

const form = useForm({
  name: null,
  code: null,
});

const submitHandler = () => form.post(route('customertypes.store'));
</script>

<template>
  <AuthenticatedLayout :title="__('customertype.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('customertype.titles.create')"
      :breadcrumbs="[{ to: 'customertypes.index', text: __('customertype.titles.entity_breadcrumb') }, { text: __('generics.actions.create') }]"
      :form="{ instance: form, submitHandler, submitText: __('generics.buttons.create'), hrefCancel: route('customertypes.index') }"
    />
    <FormCustomerType
      :form="form"
      :submitHandler="submitHandler"
    />
  </AuthenticatedLayout>
</template>
