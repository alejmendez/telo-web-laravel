<script setup>
import { useForm } from '@inertiajs/vue3';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import FormCity from '@Crm/Pages/Cities/Form.vue';

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
  <AuthenticatedLayout :title="__('city.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('city.titles.edit')"
      :breadcrumbs="[{ to: 'cities.index', text: __('city.titles.entity_breadcrumb') }, { text: __('generics.actions.edit') }]"
      :links="[{ to: route('cities.index'), text: __('generics.buttons.back') }]"
    />
    <FormCity
      :form="form"
      :readOnly="true"
      :submitHandler="submitHandler"
    />
  </AuthenticatedLayout>
</template>
