<script setup>
import { useForm } from '@inertiajs/vue3';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import FormCategory from '@Crm/Pages/Categories/Form.vue';

const props = defineProps({
  data: Object,
});

const { data } = props.data;

const form = useForm({
  _method: 'PATCH',
  id: data.id,
  name: data.name,
  slug: data.slug,
});

const submitHandler = () => form.post(route('categories.update', data.id));
</script>

<template>
  <AuthenticatedLayout :title="__('category.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('category.titles.edit')"
      :breadcrumbs="[{ to: 'categories.index', text: __('category.titles.entity_breadcrumb') }, { text: __('generics.actions.edit') }]"
      :form="{ instance: form, submitHandler, submitText: __('generics.buttons.save_edit'), hrefCancel: route('categories.index') }"
    />
    <FormCategory
      :form="form"
      :submitHandler="submitHandler"
    />
  </AuthenticatedLayout>
</template>
