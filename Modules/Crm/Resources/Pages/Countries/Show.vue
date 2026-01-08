<script setup>
import { useForm } from '@inertiajs/vue3';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import FormCountry from '@Crm/Pages/Countries/Form.vue';

const props = defineProps({
  data: Object,
});

const { data } = props.data;

const form = useForm({
  _method: 'PATCH',
  id: data.id,
  name: data.name,
  iso2: data.iso2,
});

const submitHandler = () => {};
</script>

<template>
  <AuthenticatedLayout :title="__('country.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('country.titles.edit')"
      :breadcrumbs="[{ to: 'countries.index', text: __('country.titles.entity_breadcrumb') }, { text: __('generics.actions.edit') }]"
      :links="[{ to: route('countries.index'), text: __('generics.buttons.back') }]"
    />
    <FormCountry
      :form="form"
      :readOnly="true"
      :submitHandler="submitHandler"
    />
  </AuthenticatedLayout>
</template>
