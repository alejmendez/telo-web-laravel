<script setup>
import { useForm } from '@inertiajs/vue3';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import FormCustomerType from '@Crm/Pages/CustomerTypes/Form.vue';

const props = defineProps({
  data: Object,
});

const { data } = props.data;

const form = useForm({
  id: data.id,
  name: data.name,
  code: data.code,
});

const submitHandler = () => {};
</script>

<template>
  <AuthenticatedLayout :title="__('customertype.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('customertype.titles.show')"
      :breadcrumbs="[{ to: 'customertypes.index', text: __('customertype.titles.entity_breadcrumb') }, { text: __('generics.actions.show') }]"
      :links="[{ to: route('customertypes.index'), text: __('generics.buttons.back') }]"
    />
    <FormCustomerType
      :form="form"
      :readOnly="true"
      :submitHandler="submitHandler"
    />
  </AuthenticatedLayout>
</template>
