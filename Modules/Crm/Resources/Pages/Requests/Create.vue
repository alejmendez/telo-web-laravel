<script setup>
import { useForm } from '@inertiajs/vue3';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import FormRequests from '@Crm/Pages/Requests/Form.vue';

const props = defineProps({
  customers: Array,
  categories: Array,
  urgency_types: Array,
  professionals: Array,
  statuses: Array,
});

const form = useForm({
  description: null,
  status: props.statuses.find((status) => status.value == 'pending'),
  address: null,
  priority: 1,
  sla_due_at: null,
  accepted_at: null,
  customer_id: null,
  category_id: null,
  urgency_type_id: props.urgency_types[props.urgency_types.length - 1],
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
      :categories="categories"
      :urgency_types="urgency_types"
      :professionals="professionals"
      :statuses="statuses"
      :submitHandler="submitHandler"
    />
  </AuthenticatedLayout>
</template>
