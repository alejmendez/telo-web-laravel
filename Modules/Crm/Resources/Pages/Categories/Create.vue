<script setup>
import { useForm } from '@inertiajs/vue3';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import FormCategory from '@Crm/Pages/Categories/Form.vue';

const props = defineProps();

const form = useForm({
  name: null,
  slug: null,
});

const submitHandler = () => form.post(route('categories.store'));
</script>

<template>
  <AuthenticatedLayout :title="__('category.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('category.titles.create')"
      :breadcrumbs="[{ to: 'categories.index', text: __('category.titles.entity_breadcrumb') }, { text: __('generics.actions.create') }]"
      :form="{ instance: form, submitHandler, submitText: __('generics.buttons.create'), hrefCancel: route('categories.index') }"
    />
    <FormCategory
      :form="form"
      :submitHandler="submitHandler"
    />
  </AuthenticatedLayout>
</template>
