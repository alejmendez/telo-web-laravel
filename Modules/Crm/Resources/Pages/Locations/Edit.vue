<script setup>
import { useForm } from '@inertiajs/vue3';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import FormLocation from '@Crm/Pages/Locations/Form.vue';

const props = defineProps({
  data: Object,
});

const { data } = props.data;

const form = useForm({
  _method: 'PATCH',
  id: data.id,
  name: data.name,
  type: data.type,
  country_code: data.country_code,
  parent_id: data.parent_id,
  level: data.level,
  code: data.code,
});

const submitHandler = () => form.post(route('locations.update', data.id));
</script>

<template>
  <AuthenticatedLayout :title="__('location.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('location.titles.edit')"
      :breadcrumbs="[{ to: 'locations.index', text: __('location.titles.entity_breadcrumb') }, { text: __('generics.actions.edit') }]"
      :form="{ instance: form, submitHandler, submitText: __('generics.buttons.save_edit'), hrefCancel: route('locations.index') }"
    />
    <FormLocation
      :form="form"
      :submitHandler="submitHandler"
    />
  </AuthenticatedLayout>
</template>
