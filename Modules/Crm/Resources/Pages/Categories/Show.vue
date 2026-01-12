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
  id: data.id,
  name: data.name,
  slug: data.slug,
});

const submitHandler = () => {};
</script>

<template>
  <AuthenticatedLayout :title="__('category.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('category.titles.show')"
      :breadcrumbs="[{ to: 'categories.index', text: __('category.titles.entity_breadcrumb') }, { text: __('generics.actions.show') }]"
      :links="[{ to: route('categories.index'), text: __('generics.buttons.back') }]"
    />
    <FormCategory
      :form="form"
      :readOnly="true"
      :submitHandler="submitHandler"
    />
  </AuthenticatedLayout>
</template>
