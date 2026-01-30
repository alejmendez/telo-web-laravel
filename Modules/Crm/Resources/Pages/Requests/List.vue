<script setup>
import { ref, onMounted, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from 'primevue/useconfirm';
import { trans } from 'laravel-vue-i18n';

import { FilterMatchMode, FilterOperator } from '@primevue/core/api';
import InputText from 'primevue/inputtext';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import Datatable from '@Core/Components/Table/Datatable.vue';
import RequestsService from '@Crm/Services/RequestsService.js';
import { defaultDeleteHandler } from '@Core/Utils/table.js';
import { stringToFormat } from '@Core/Utils/date.js';

import { can } from '@Auth/Services/Auth';

const props = defineProps({
  toast: Object,
});

const toast = useToast();
const confirm = useConfirm();

const datatable = ref(null);

const filters = {
  global: { value: null, matchMode: FilterMatchMode.CONTAINS },
  title: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.CONTAINS }] },
  status: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.CONTAINS }] },
};

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

const columns = computed(() => [
  { field: 'title', header: trans('requests.table.title.label'), sortable: true, frozen: true, style: 'min-width: 200px' },
  { field: 'customer.full_name', header: trans('requests.table.customer.label'), style: 'min-width: 200px' },
  { field: 'status', header: trans('requests.table.status.label'), sortable: true, style: 'min-width: 150px' },
  { field: 'priority', header: trans('requests.table.priority.label'), sortable: true, style: 'min-width: 150px' },
  { field: 'sla_due_at', header: trans('requests.table.sla_due_at.label'), sortable: true, style: 'min-width: 200px' },
  { type: 'actions', style: 'min-width: 130px', exportable: false },
]);

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
      sortField="title"
      :sortOrder="1"
      :columns="columns"
    >
      <template #filter-title="{ filterModel }">
        <InputText v-model="filterModel.value" type="text" :placeholder="__('requests.table.title.placeholder')" />
      </template>

      <template #filter-customer.full_name="{ filterModel }">
        <InputText v-model="filterModel.value" type="text" :placeholder="__('requests.table.customer.placeholder')" />
      </template>

      <template #filter-status="{ filterModel }">
        <InputText v-model="filterModel.value" type="text" :placeholder="__('requests.table.status.placeholder')" />
      </template>

      <template #filter-priority="{ filterModel }">
        <InputText v-model="filterModel.value" type="text" :placeholder="__('requests.table.priority.placeholder')" />
      </template>

      <template #body-sla_due_at="{ data }">
        {{ stringToFormat(data.sla_due_at) }}
      </template>
      <template #filter-sla_due_at="{ filterModel }">
        <InputText v-model="filterModel.value" type="text" :placeholder="__('requests.table.sla_due_at.placeholder')" />
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
