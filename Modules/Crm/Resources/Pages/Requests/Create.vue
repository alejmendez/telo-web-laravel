<script setup>
import { useForm } from '@inertiajs/vue3';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import FormRequests from '@Crm/Pages/Requests/Form.vue';

const props = defineProps();

const form = useForm({
  name: null,
});

const submitHandler = () => form.post(route('requests.store'));
</script>

<template>
  <AuthenticatedLayout :title="__('requests.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('requests.titles.create')"
      :breadcrumbs="[{ to: 'requests.index', text: __('requests.titles.entity_breadcrumb') }, { text: __('generics.actions.create') }]"
      :form="{ instance: form, submitHandler, submitText: __('generics.buttons.create'), hrefCancel: route('requests.index') }"
    />
    <FormRequests
      :form="form"
      :submitHandler="submitHandler"
    />
  </AuthenticatedLayout>
</template>
