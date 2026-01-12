<script setup>
import { useForm } from '@inertiajs/vue3';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import FormContactType from '@Crm/Pages/ContactTypes/Form.vue';

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

const submitHandler = () => form.post(route('contacttypes.update', data.id));
</script>

<template>
  <AuthenticatedLayout :title="__('contacttype.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('contacttype.titles.edit')"
      :breadcrumbs="[{ to: 'contacttypes.index', text: __('contacttype.titles.entity_breadcrumb') }, { text: __('generics.actions.edit') }]"
      :form="{ instance: form, submitHandler, submitText: __('generics.buttons.save_edit'), hrefCancel: route('contacttypes.index') }"
    />
    <FormContactType
      :form="form"
      :submitHandler="submitHandler"
    />
  </AuthenticatedLayout>
</template>
