<script setup>
import { useForm } from '@inertiajs/vue3';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import FormServices from '@Crm/Pages/Services/Form.vue';

const props = defineProps({
  data: Object,
});

const { data } = props.data;

const form = useForm({
  _method: 'PATCH',
  id: data.id,
  name: data.name,
});

const submitHandler = () => form.post(route('services.update', data.id));
</script>

<template>
  <AuthenticatedLayout :title="__('services.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('services.titles.edit')"
      :breadcrumbs="[{ to: 'services.index', text: __('services.titles.entity_breadcrumb') }, { text: __('generics.actions.edit') }]"
      :form="{ instance: form, submitHandler, submitText: __('generics.buttons.save_edit'), hrefCancel: route('services.index') }"
    />
    <FormServices
      :form="form"
      :submitHandler="submitHandler"
    />
  </AuthenticatedLayout>
</template>
