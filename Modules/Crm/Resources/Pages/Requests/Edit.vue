<script setup>
import { useForm } from '@inertiajs/vue3';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import FormRequests from '@Crm/Pages/Requests/Form.vue';
import { stringToDate } from '@Core/Utils/date';

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
  _method: 'PATCH',
  id: data.id,
  description: data.description,
  status: props.statuses.find((status) => status.value == data.status),
  address: data.address,
  priority: data.priority,
  sla_due_at: stringToDate(data.sla_due_at),
  accepted_at: stringToDate(data.accepted_at),
  customer_id: props.customers.find((customer) => customer.value == data.customer_id),
  category_id: props.categories.find((category) => category.value == data.category_id),
  urgency_type_id: props.urgency_types.find((urgency_type) => urgency_type.value == data.urgency_type_id),
  professional_id: props.professionals.find((professional) => professional.value == data.professional_id),
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
      :categories="categories"
      :urgency_types="urgency_types"
      :professionals="professionals"
      :statuses="statuses"
      :submitHandler="submitHandler"
    />
  </AuthenticatedLayout>
</template>
