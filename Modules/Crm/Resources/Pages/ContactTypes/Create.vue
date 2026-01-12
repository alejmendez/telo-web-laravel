<script setup>
import { useForm } from '@inertiajs/vue3';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import FormContactType from '@Crm/Pages/ContactTypes/Form.vue';

const props = defineProps();

const form = useForm({
  name: null,
  code: null,
});

const submitHandler = () => form.post(route('contacttypes.store'));
</script>

<template>
  <AuthenticatedLayout :title="__('contacttype.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('contacttype.titles.create')"
      :breadcrumbs="[{ to: 'contacttypes.index', text: __('contacttype.titles.entity_breadcrumb') }, { text: __('generics.actions.create') }]"
      :form="{ instance: form, submitHandler, submitText: __('generics.buttons.create'), hrefCancel: route('contacttypes.index') }"
    />
    <FormContactType
      :form="form"
      :submitHandler="submitHandler"
    />
  </AuthenticatedLayout>
</template>
