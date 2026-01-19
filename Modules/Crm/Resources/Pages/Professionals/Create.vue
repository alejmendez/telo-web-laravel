<script setup>
import { useForm } from '@inertiajs/vue3';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import FormProfessional from '@Crm/Pages/Professionals/Form.vue';

const props = defineProps();

const form = useForm({
  name: null,
});

const submitHandler = () => form.post(route('professionals.store'));
</script>

<template>
  <AuthenticatedLayout :title="__('professional.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('professional.titles.create')"
      :breadcrumbs="[{ to: 'professionals.index', text: __('professional.titles.entity_breadcrumb') }, { text: __('generics.actions.create') }]"
      :form="{ instance: form, submitHandler, submitText: __('generics.buttons.create'), hrefCancel: route('professionals.index') }"
    />
    <FormProfessional
      :form="form"
      :submitHandler="submitHandler"
    />
  </AuthenticatedLayout>
</template>
