<script setup>
import { useForm } from '@inertiajs/vue3';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import FormRequests from '@Crm/Pages/Requests/Form.vue';

const props = defineProps({
  customers: Array,
  subcategories: Array,
  urgency_types: Array,
  professionals: Array,
});

const form = useForm({
  title: null,
  description: null,
  status: 'pending',
  priority: null,
  sla_due_at: null,
  accepted_at: null,
  customer_id: null,
  subcategory_id: null,
  urgency_type_id: null,
  assigned_professional_id: null,
});

const submitHandler = () => form.post(route('requests.store'));
</script>

<template>
  <AuthenticatedLayout :title="__('requests.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('requests.titles.create')"
      :breadcrumbs="[{ to: 'requests.index', text: __('requests.titles.entity_breadcrumb') }, { text: __('generics.actions.create') }]"
      :form="{ instance: form, submitHandler, submitText: __('generics.buttons.create'), hrefCancel: route('requests.index') }"
    />
    <FormRequests
      :form="form"
      :customers="customers"
      :subcategories="subcategories"
      :urgency_types="urgency_types"
      :professionals="professionals"
      :submitHandler="submitHandler"
    />
  </AuthenticatedLayout>
</template>
