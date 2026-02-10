<script setup>
import { useForm } from '@inertiajs/vue3';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import FormServices from '@Crm/Pages/Services/Form.vue';
import { stringToDate } from '@Core/Utils/date';

const props = defineProps({
  data: Object,
  requests: Array,
  professionals: Array,
  statuses: Array,
});

const { data } = props.data;

const form = useForm({
  _method: 'PATCH',
  id: data.id,
  request_id: props.requests.find((item) => item.value === data.request_id),
  professional_id: props.professionals.find((item) => item.value === data.professional_id),
  status: props.statuses.find((item) => item.value === data.status),
  started_at: stringToDate(data.started_at),
  completed_at: stringToDate(data.completed_at),
});

const submitHandler = () => {};
</script>

<template>
  <AuthenticatedLayout :title="__('services.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('services.titles.show')"
      :breadcrumbs="[{ to: 'services.index', text: __('services.titles.entity_breadcrumb') }, { text: __('generics.actions.show') }]"
      :links="[{ to: route('services.index'), text: __('generics.buttons.back') }]"
    />
    <FormServices
      :form="form"
      :readOnly="true"
      :requests="props.requests"
      :customers="props.customers"
      :professionals="props.professionals"
      :statuses="props.statuses"
      :submitHandler="submitHandler"
    />
  </AuthenticatedLayout>
</template>
