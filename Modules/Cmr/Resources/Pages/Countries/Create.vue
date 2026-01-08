<script setup>
import { useForm } from '@inertiajs/vue3';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import FormCountry from '@Cmr/Pages/Countries/Form.vue';

const props = defineProps();

const form = useForm({
  name: null,
  iso2: null,
});

const submitHandler = () => form.post(route('countries.store'));
</script>

<template>
  <AuthenticatedLayout :title="__('country.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('country.titles.create')"
      :breadcrumbs="[{ to: 'countries.index', text: __('country.titles.entity_breadcrumb') }, { text: __('generics.actions.create') }]"
      :form="{ instance: form, submitHandler, submitText: __('generics.buttons.create'), hrefCancel: route('countries.index') }"
    />
    <FormCountry
      :form="form"
      :submitHandler="submitHandler"
    />
  </AuthenticatedLayout>
</template>
