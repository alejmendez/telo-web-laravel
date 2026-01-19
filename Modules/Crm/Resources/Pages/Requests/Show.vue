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
  id: data.id,
  name: data.name,
});

const submitHandler = () => {};
</script>

<template>
  <AuthenticatedLayout :title="__('requests.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('requests.titles.show')"
      :breadcrumbs="[{ to: 'requests.index', text: __('requests.titles.entity_breadcrumb') }, { text: __('generics.actions.show') }]"
      :links="[{ to: route('requests.index'), text: __('generics.buttons.back') }]"
    />
    <FormRequests
      :form="form"
      :readOnly="true"
      :submitHandler="submitHandler"
    />
  </AuthenticatedLayout>
</template>
