<script setup>
import { useForm } from '@inertiajs/vue3';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import FormServices from '@Crm/Pages/Services/Form.vue';

const props = defineProps();

const form = useForm({
  name: null,
});

const submitHandler = () => form.post(route('services.store'));
</script>

<template>
  <AuthenticatedLayout :title="__('services.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('services.titles.create')"
      :breadcrumbs="[{ to: 'services.index', text: __('services.titles.entity_breadcrumb') }, { text: __('generics.actions.create') }]"
      :form="{ instance: form, submitHandler, submitText: __('generics.buttons.create'), hrefCancel: route('services.index') }"
    />
    <FormServices
      :form="form"
      :submitHandler="submitHandler"
    />
  </AuthenticatedLayout>
</template>
