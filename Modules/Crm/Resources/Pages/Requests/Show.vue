<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Drawer from 'primevue/drawer';
import Button from 'primevue/button';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import FormRequests from '@Crm/Pages/Requests/Form.vue';
import { stringToDate, stringToFormat } from '@Core/Utils/date.js';

const props = defineProps({
  data: Object,
  customers: Array,
  categories: Array,
  urgency_types: Array,
  professionals: Array,
  statuses: Array,
  histories: Array,
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

const visible_request_history = ref(false);

const submitHandler = () => {};

const showHistory = () => {
  visible_request_history.value = true;
};
</script>

<template>
  <AuthenticatedLayout :title="__('requests.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('requests.titles.show')"
      :breadcrumbs="[{ to: 'requests.index', text: __('requests.titles.entity_breadcrumb') }, { text: __('generics.actions.show') }]"
      :links="[{ to: route('requests.index'), text: __('generics.buttons.back') }]"
    >
      <Button
        @click="showHistory"
        :disabled="props.form?.instance.processing"
        :loading="props.form?.instance.processing"
        :label="__('requests.history.button.show_history')"
      />
    </HeaderCrud>
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

    <Drawer
      v-model:visible="visible_request_history"
      :header="__('requests.history.title')"
      position="right"
      class="!w-full md:!w-80 lg:!w-[30rem]"
    >
      <div class="border-b border-gray-200 py-4" v-for="history in histories" :key="history.id">
        <p>{{ stringToFormat(history.updated_at, 'dd/MM/yyyy HH:mm:ss') }} - {{ __('requests.history.operations.' + history.operation) }}</p>
        <ul class="list-disc pl-5">
          <li>{{ __('requests.form.status.label') }}: {{ __('statuses.' + history.data.status) }}</li>
        </ul>
      </div>
    </Drawer>
  </AuthenticatedLayout>
</template>
