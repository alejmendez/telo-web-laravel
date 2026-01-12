<script setup>
import { useForm } from '@inertiajs/vue3';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import FormProfessionalType from '@Crm/Pages/ProfessionalTypes/Form.vue';

const props = defineProps();

const form = useForm({
  name: null,
  code: null,
});

const submitHandler = () => form.post(route('professionaltypes.store'));
</script>

<template>
  <AuthenticatedLayout :title="__('professionaltype.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('professionaltype.titles.create')"
      :breadcrumbs="[{ to: 'professionaltypes.index', text: __('professionaltype.titles.entity_breadcrumb') }, { text: __('generics.actions.create') }]"
      :form="{ instance: form, submitHandler, submitText: __('generics.buttons.create'), hrefCancel: route('professionaltypes.index') }"
    />
    <FormProfessionalType
      :form="form"
      :submitHandler="submitHandler"
    />
  </AuthenticatedLayout>
</template>
