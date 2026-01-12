<script setup>
import { useForm } from '@inertiajs/vue3';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import FormProfessionalType from '@Crm/Pages/ProfessionalTypes/Form.vue';

const props = defineProps({
  data: Object,
});

const { data } = props.data;

const form = useForm({
  _method: 'PATCH',
  id: data.id,
  name: data.name,
  code: data.code,
});

const submitHandler = () => form.post(route('professionaltypes.update', data.id));
</script>

<template>
  <AuthenticatedLayout :title="__('professionaltype.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('professionaltype.titles.edit')"
      :breadcrumbs="[{ to: 'professionaltypes.index', text: __('professionaltype.titles.entity_breadcrumb') }, { text: __('generics.actions.edit') }]"
      :form="{ instance: form, submitHandler, submitText: __('generics.buttons.save_edit'), hrefCancel: route('professionaltypes.index') }"
    />
    <FormProfessionalType
      :form="form"
      :submitHandler="submitHandler"
    />
  </AuthenticatedLayout>
</template>
