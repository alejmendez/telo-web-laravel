<script setup>
import { useForm } from '@inertiajs/vue3';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import FormCity from '@Crm/Pages/Cities/Form.vue';

const props = defineProps();

const form = useForm({
  name: null,
});

const submitHandler = () => form.post(route('cities.store'));
</script>

<template>
  <AuthenticatedLayout :title="__('city.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('city.titles.create')"
      :breadcrumbs="[{ to: 'cities.index', text: __('city.titles.entity_breadcrumb') }, { text: __('generics.actions.create') }]"
      :form="{ instance: form, submitHandler, submitText: __('generics.buttons.create'), hrefCancel: route('cities.index') }"
    />
    <FormCity
      :form="form"
      :submitHandler="submitHandler"
    />
  </AuthenticatedLayout>
</template>
