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
  _method: 'PATCH',
  id: data.id,
  name: data.name,
  country_id: props.countries.find((item) => item.value === data.country_id.value),
});

const submitHandler = () => form.post(route('cities.update', data.id));
</script>

<template>
  <AuthenticatedLayout :title="__('city.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('city.titles.edit')"
      :breadcrumbs="[{ to: 'cities.index', text: __('city.titles.entity_breadcrumb') }, { text: __('generics.actions.edit') }]"
      :form="{ instance: form, submitHandler, submitText: __('generics.buttons.save_edit'), hrefCancel: route('cities.index') }"
    />
    <FormCity
      :form="form"
      :submitHandler="submitHandler"
      :countries="props.countries"
    />
  </AuthenticatedLayout>
</template>
