<script setup>
import { useForm } from '@inertiajs/vue3';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import FormUrgencyType from '@Crm/Pages/UrgencyTypes/Form.vue';

const props = defineProps();

const form = useForm({
  name: null,
  code: null,
  priority_weight: null,
  sla_hours: null,
});

const submitHandler = () => form.post(route('urgencytypes.store'));
</script>

<template>
  <AuthenticatedLayout :title="__('urgencytype.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('urgencytype.titles.create')"
      :breadcrumbs="[{ to: 'urgencytypes.index', text: __('urgencytype.titles.entity_breadcrumb') }, { text: __('generics.actions.create') }]"
      :form="{ instance: form, submitHandler, submitText: __('generics.buttons.create'), hrefCancel: route('urgencytypes.index') }"
    />
    <FormUrgencyType
      :form="form"
      :submitHandler="submitHandler"
    />
  </AuthenticatedLayout>
</template>
