<script setup>
import { useForm } from '@inertiajs/vue3';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import FormCity from '@Crm/Pages/Cities/Form.vue';

const props = defineProps({
  data: Object,
  countries: Array,
});

const { data } = props.data;

const form = useForm({
  name: data.name,
  country_id: props.countries.find((item) => item.value === data.country_id.value),
});

const submitHandler = () => {};
</script>

<template>
  <AuthenticatedLayout :title="__('city.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('city.titles.show')"
      :breadcrumbs="[{ to: 'cities.index', text: __('city.titles.entity_breadcrumb') }, { text: __('generics.actions.show') }]"
      :links="[{ to: route('cities.index'), text: __('generics.buttons.back') }]"
    />
    <FormCity
      :form="form"
      :countries="props.countries"
      :readOnly="true"
      :submitHandler="submitHandler"
    />
  </AuthenticatedLayout>
</template>
