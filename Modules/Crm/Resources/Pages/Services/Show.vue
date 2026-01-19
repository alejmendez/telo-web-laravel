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
  id: data.id,
  name: data.name,
});

const submitHandler = () => {};
</script>

<template>
  <AuthenticatedLayout :title="__('services.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('services.titles.show')"
      :breadcrumbs="[{ to: 'services.index', text: __('services.titles.entity_breadcrumb') }, { text: __('generics.actions.show') }]"
      :links="[{ to: route('services.index'), text: __('generics.buttons.back') }]"
    />
    <FormServices
      :form="form"
      :readOnly="true"
      :submitHandler="submitHandler"
    />
  </AuthenticatedLayout>
</template>
