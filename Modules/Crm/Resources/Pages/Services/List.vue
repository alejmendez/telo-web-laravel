<script setup>
import { ref, computed, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from 'primevue/useconfirm';
import DatePicker from 'primevue/datepicker';
import { trans } from 'laravel-vue-i18n';

import { FilterMatchMode } from '@primevue/core/api';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import Datatable from '@Core/Components/Table/Datatable.vue';
import ServicesService from '@Crm/Services/ServicesService.js';
import { defaultDeleteHandler, debouncedFilter } from '@Core/Utils/table.js';
import { stringToFormat } from '@Core/Utils/date.js';

import { can } from '@Auth/Services/Auth';

const props = defineProps({
  toast: Object,
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
  'request.description': { value: null, matchMode: FilterMatchMode.CONTAINS },
  'request.address': { value: null, matchMode: FilterMatchMode.CONTAINS },
};

const canShow = can('services.show');
const canEdit = can('services.edit');
const canDestroy = can('services.destroy');
const canCreate = can('services.create');

const headerLinks = [];
if (canCreate) {
  headerLinks.push({ to: 'services.create', text: 'generics.new' });
}

const fetchHandler = async (params) => {
  return await ServicesService.list(params);
};

const deleteHandler = (record) => {
  defaultDeleteHandler(confirm, datatable, toast, () => ServicesService.del(record.id));
};

const columns = computed(() => [
  { field: 'created_at', header: trans('services.table.created_at.label'), sortable: true, style: 'min-width: 200px' },
  { field: 'customer.full_name', header: trans('services.table.customer.label'), sortable: true, style: 'min-width: 200px' },
  { field: 'professional.full_name', header: trans('services.table.professional.label'), sortable: true, style: 'min-width: 200px' },
  { field: 'status', header: trans('services.table.status.label'), sortable: true, style: 'min-width: 150px' },
  { field: 'request.description', header: trans('services.table.description.label'), sortable: true, style: 'min-width: 200px' },
  { field: 'request.address', header: trans('services.table.address.label'), sortable: true, style: 'min-width: 200px' },
  { type: 'actions', style: 'min-width: 130px', exportable: false },
]);

onMounted(async () => {
  if (props.toast) {
    toast.add(props.toast);
  }
});
</script>

<template>
  <AuthenticatedLayout :title="__('services.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('services.titles.entity_breadcrumb')"
      :breadcrumbs="[{ to: 'services.index', text: __('services.titles.entity_breadcrumb') }, { text: __('generics.list') }]"
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
      <template #filter-created_at="{ filterModel, filterCallback }">
        <DatePicker v-model="filterModel.value" @value-change="debouncedFilter(filterCallback)" showClear :placeholder="__('services.table.created_at.placeholder')" />
      </template>

      <template #filter-customer.full_name="{ filterModel, filterCallback }">
        <InputText v-model="filterModel.value" type="text" @input="debouncedFilter(filterCallback)" :placeholder="__('services.table.customer.placeholder')" />
      </template>

      <template #filter-professional.full_name="{ filterModel, filterCallback }">
        <InputText v-model="filterModel.value" type="text" @input="debouncedFilter(filterCallback)" :placeholder="__('services.table.professional.placeholder')" />
      </template>

      <template #filter-status="{ filterModel, filterCallback }">
        <Select v-model="filterModel.value" :options="props.statuses" optionLabel="text" optionValue="value" showClear @change="filterCallback()" :placeholder="__('services.table.status.placeholder')" />
      </template>

      <template #filter-request.description="{ filterModel, filterCallback }">
        <InputText v-model="filterModel.value" type="text" @input="debouncedFilter(filterCallback)" :placeholder="__('services.table.description.placeholder')" />
      </template>

      <template #filter-request.address="{ filterModel, filterCallback }">
        <InputText v-model="filterModel.value" type="text" @input="debouncedFilter(filterCallback)" :placeholder="__('services.table.address.placeholder')" />
      </template>

       <template #body-actions="{ data }">
        <Link :href="route('services.show', data.id)" v-if="canShow">
          <span class="material-symbols-rounded cursor-pointer transition-all text-slate-500 hover:text-sky-600">visibility</span>
        </Link>
        <Link :href="route('services.edit', data.id)" v-if="canEdit">
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
