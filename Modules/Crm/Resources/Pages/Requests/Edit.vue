<script setup>
import { useForm } from '@inertiajs/vue3';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import FormRequests from '@Crm/Pages/Requests/Form.vue';

const props = defineProps({
  data: Object,
  customers: Array,
  subcategories: Array,
  urgency_types: Array,
  professionals: Array,
});

const { data } = props.data;

const form = useForm({
  _method: 'PATCH',
  id: data.id,
  title: data.title,
  description: data.description,
  status: data.status,
  priority: data.priority,
  sla_due_at: data.sla_due_at,
  accepted_at: data.accepted_at,
  customer_id: data.customer_id,
  subcategory_id: data.subcategory_id,
  urgency_type_id: data.urgency_type_id,
  assigned_professional_id: data.assigned_professional_id,
});

const submitHandler = () => form.post(route('requests.update', data.id));
</script>

<template>
  <AuthenticatedLayout :title="__('requests.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('requests.titles.edit')"
      :breadcrumbs="[{ to: 'requests.index', text: __('requests.titles.entity_breadcrumb') }, { text: __('generics.actions.edit') }]"
      :form="{ instance: form, submitHandler, submitText: __('generics.buttons.save_edit'), hrefCancel: route('requests.index') }"
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
