<script setup>
import { useForm } from '@inertiajs/vue3';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import FormRequests from '@Crm/Pages/Requests/Form.vue';

const props = defineProps({
  data: Object,
});

const { data } = props.data;

const form = useForm({
  _method: 'PATCH',
  id: data.id,
  name: data.name,
});

const submitHandler = () => form.post(route('requests.update', data.id));
</script>

<template>
  <AuthenticatedLayout :title="__('requests.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('requests.titles.edit')"
      :breadcrumbs="[{ to: 'requests.index', text: __('requests.titles.entity_breadcrumb') }, { text: __('generics.actions.edit') }]"
      :form="{ instance: form, submitHandler, submitText: __('generics.buttons.save_edit'), hrefCancel: route('requests.index') }"
    />
    <FormRequests
      :form="form"
      :submitHandler="submitHandler"
    />
  </AuthenticatedLayout>
</template>
