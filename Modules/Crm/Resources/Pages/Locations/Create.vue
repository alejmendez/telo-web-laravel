<script setup>
import { useForm } from '@inertiajs/vue3';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import FormLocation from '@Crm/Pages/Locations/Form.vue';

const props = defineProps();

const form = useForm({
  name: null,
  type: null,
  country_code: null,
  parent_id: null,
  level: null,
  code: null,
});

const submitHandler = () => form.post(route('locations.store'));
</script>

<template>
  <AuthenticatedLayout :title="__('location.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('location.titles.create')"
      :breadcrumbs="[{ to: 'locations.index', text: __('location.titles.entity_breadcrumb') }, { text: __('generics.actions.create') }]"
      :form="{ instance: form, submitHandler, submitText: __('generics.buttons.create'), hrefCancel: route('locations.index') }"
    />
    <FormLocation
      :form="form"
      :submitHandler="submitHandler"
    />
  </AuthenticatedLayout>
</template>
