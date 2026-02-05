<script setup>
import { useForm } from '@inertiajs/vue3';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import FormRequests from '@Crm/Pages/Requests/Form.vue';

const props = defineProps({
  data: Object,
  customers: Array,
  categories: Array,
  urgency_types: Array,
  professionals: Array,
  statuses: Array,
});

const { data } = props.data;

const form = useForm({
  id: data.id,
  customer_id: data.customer_id,
  category_id: data.category_id,
  urgency_type_id: data.urgency_type_id,
  assigned_professional_id: data.assigned_professional_id,
  description: data.description,
  status: data.status,
  address: data.address,
  priority: data.priority,
  sla_due_at: data.sla_due_at,
  accepted_at: data.accepted_at,
});

const submitHandler = () => {};
</script>

<template>
  <AuthenticatedLayout :title="__('requests.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('requests.titles.show')"
      :breadcrumbs="[{ to: 'requests.index', text: __('requests.titles.entity_breadcrumb') }, { text: __('generics.actions.show') }]"
      :links="[{ to: route('requests.index'), text: __('generics.buttons.back') }]"
    />
    <FormRequests
      :form="form"
      :customers="customers"
      :categories="categories"
      :urgency_types="urgency_types"
      :professionals="professionals"
      :statuses="statuses"
      :readOnly="true"
      :submitHandler="submitHandler"
    />
  </AuthenticatedLayout>
</template>
