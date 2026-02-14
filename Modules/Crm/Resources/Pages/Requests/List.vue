<script setup>
import { ref, onMounted, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from 'primevue/useconfirm';
import { trans } from 'laravel-vue-i18n';

import { FilterMatchMode } from '@primevue/core/api';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import DatePicker from 'primevue/datepicker';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import Datatable from '@Core/Components/Table/Datatable.vue';
import RequestsService from '@Crm/Services/RequestsService.js';
import { defaultDeleteHandler, debouncedFilter } from '@Core/Utils/table.js';
import { stringToFormat } from '@Core/Utils/date.js';

import { can } from '@Auth/Services/Auth';

const props = defineProps({
  toast: Object,
  categories: Array,
  urgency_types: Array,
  customers: Array,
  professionals: Array,
  statuses: Array,
});

const toast = useToast();
const confirm = useConfirm();

const datatable = ref(null);

const filters = {
  global: { value: null, matchMode: FilterMatchMode.CONTAINS },
  created_at: { value: null, matchMode: FilterMatchMode.CONTAINS },
  'customer.full_name': { value: null, matchMode: FilterMatchMode.CONTAINS },
  'professional.full_name': { value: null, matchMode: FilterMatchMode.CONTAINS },
  status: { value: null, matchMode: FilterMatchMode.CONTAINS },
  description: { value: null, matchMode: FilterMatchMode.CONTAINS },
  address: { value: null, matchMode: FilterMatchMode.CONTAINS },
  'urgency_type.name': { value: null, matchMode: FilterMatchMode.CONTAINS },
  priority: { value: null, matchMode: FilterMatchMode.CONTAINS },
  sla_due_at: { value: null, matchMode: FilterMatchMode.CONTAINS },
};

const columns = computed(() => [
  { field: 'created_at', header: trans('requests.table.created_at.label'), sortable: true, style: 'min-width: 200px' },
  { field: 'customer.full_name', header: trans('requests.table.customer.label'), sortable: true, style: 'min-width: 200px' },
  { field: 'professional.full_name', header: trans('requests.table.professional.label'), sortable: true, style: 'min-width: 200px' },
  { field: 'status', header: trans('requests.table.status.label'), sortable: true, style: 'min-width: 150px' },
  { field: 'description', header: trans('requests.table.description.label'), sortable: true, style: 'min-width: 200px' },
  { field: 'address', header: trans('requests.table.address.label'), sortable: true, style: 'min-width: 200px' },
  { field: 'urgency_type.name', header: trans('requests.table.urgency_type.label'), sortable: true, style: 'min-width: 150px' },
  { field: 'priority', header: trans('requests.table.priority.label'), sortable: true, style: 'min-width: 150px' },
  { field: 'sla_due_at', header: trans('requests.table.sla_due_at.label'), sortable: true, style: 'min-width: 200px' },
  { type: 'actions', style: 'min-width: 130px', exportable: false },
]);

const canShow = can('requests.show');
const canEdit = can('requests.edit');
const canDestroy = can('requests.destroy');
const canCreate = can('requests.create');

const headerLinks = [];
if (canCreate) {
  headerLinks.push({ to: 'requests.create', text: 'generics.new' });
}

const fetchHandler = async (params) => {
  return await RequestsService.list(params);
};

const deleteHandler = (record) => {
  defaultDeleteHandler(confirm, datatable, toast, () => RequestsService.del(record.id));
};

onMounted(async () => {
  if (props.toast) {
    toast.add(props.toast);
  }
});
</script>

<template>
  <AuthenticatedLayout :title="__('requests.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('requests.titles.entity_breadcrumb')"
      :breadcrumbs="[{ to: 'requests.index', text: __('requests.titles.entity_breadcrumb') }, { text: __('generics.list') }]"
      :links="headerLinks"
    />

    <Datatable
      ref="datatable"
      :filters="filters"
      :fetchHandler="fetchHandler"
      sortField="status"
      :sortOrder="1"
      :columns="columns"
    >
      <template #body-created_at="{ data }">
        {{ stringToFormat(data.created_at) }}
      </template>
      <template #body-status="{ data }">
        {{ props.statuses.find((status) => status.value == data.status)?.text || '-' }}
      </template>
      <template #filter-created_at="{ filterModel, filterCallback }">
        <DatePicker v-model="filterModel.value" @value-change="debouncedFilter(filterCallback)" showClear :placeholder="__('requests.table.created_at.placeholder')" />
      </template>

      <template #filter-customer.full_name="{ filterModel, filterCallback }">
        <InputText v-model="filterModel.value" @input="debouncedFilter(filterCallback)" type="text" :placeholder="__('requests.table.customer.placeholder')" />
      </template>

      <template #filter-professional.full_name="{ filterModel, filterCallback }">
        <InputText v-model="filterModel.value" @input="debouncedFilter(filterCallback)" type="text" :placeholder="__('requests.table.professional.placeholder')" />
      </template>

      <template #filter-status="{ filterModel, filterCallback }">
        <Select v-model="filterModel.value" :options="props.statuses" optionLabel="text" optionValue="value" showClear @change="filterCallback()" :placeholder="__('requests.table.status.placeholder')" />
      </template>

      <template #filter-description="{ filterModel, filterCallback }">
        <InputText v-model="filterModel.value" @input="debouncedFilter(filterCallback)" type="text" :placeholder="__('requests.table.description.placeholder')" />
      </template>

      <template #filter-address="{ filterModel, filterCallback }">
        <InputText v-model="filterModel.value" @input="debouncedFilter(filterCallback)" type="text" :placeholder="__('requests.table.address.placeholder')" />
      </template>

      <template #filter-priority="{ filterModel, filterCallback }">
        <InputText v-model="filterModel.value" @input="debouncedFilter(filterCallback)" type="text" :placeholder="__('requests.table.priority.placeholder')" />
      </template>

      <template #filter-urgency_type.name="{ filterModel, filterCallback }">
        <Select v-model="filterModel.value" :options="props.urgency_types" optionLabel="text" optionValue="value" showClear @change="filterCallback()" :placeholder="__('requests.table.urgency_type.placeholder')" />
      </template>

      <template #body-sla_due_at="{ data }">
        {{ stringToFormat(data.sla_due_at) }}
      </template>
      <template #filter-sla_due_at="{ filterModel, filterCallback }">
        <DatePicker v-model="filterModel.value" @value-change="debouncedFilter(filterCallback)" showClear :placeholder="__('requests.table.sla_due_at.placeholder')" />
      </template>

      <template #body-actions="{ data }">
        <Link :href="route('requests.show', data.id)" v-if="canShow">
          <span class="material-symbols-rounded cursor-pointer transition-all text-slate-500 hover:text-sky-600">visibility</span>
        </Link>
        <Link :href="route('requests.edit', data.id)" v-if="canEdit">
          <span class="material-symbols-rounded cursor-pointer transition-all text-slate-500 hover:text-emerald-600">edit</span>
        </Link>
        <span
          class="material-symbols-rounded cursor-pointer transition-all text-slate-500 hover:text-pink-600"
          @click="deleteHandler(data)"
          v-if="canDestroy"
        >
          delete
        </span>
      </template>
    </Datatable>
  </AuthenticatedLayout>
</template>
