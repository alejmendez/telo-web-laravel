<script setup>
import { ref, computed, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from 'primevue/useconfirm';

import { FilterMatchMode } from '@primevue/core/api';
import InputText from 'primevue/inputtext';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import Datatable from '@Core/Components/Table/Datatable.vue';
import UrgencyTypeService from '@Crm/Services/UrgencyTypeService.js';
import { defaultDeleteHandler, debouncedFilter } from '@Core/Utils/table.js';
import { trans } from 'laravel-vue-i18n';

import { can } from '@Auth/Services/Auth';

const props = defineProps({
  toast: Object,
});

const toast = useToast();
const confirm = useConfirm();

const datatable = ref(null);

const filters = {
  global: { value: null, matchMode: FilterMatchMode.CONTAINS },
  name: { value: null, matchMode: FilterMatchMode.CONTAINS },
  code: { value: null, matchMode: FilterMatchMode.CONTAINS },
  priority_weight: { value: null, matchMode: FilterMatchMode.CONTAINS },
  sla_hours: { value: null, matchMode: FilterMatchMode.CONTAINS },
};

const columns = computed(() => [
  { field: 'name', header: trans('urgencytype.table.name.label'), sortable: true, style: 'min-width: 200px' },
  { field: 'code', header: trans('urgencytype.table.code.label'), sortable: true, style: 'min-width: 200px' },
  { field: 'priority_weight', header: trans('urgencytype.table.priority_weight.label'), sortable: true, style: 'min-width: 200px' },
  { field: 'sla_hours', header: trans('urgencytype.table.sla_hours.label'), sortable: true, style: 'min-width: 200px' },
  { type: 'actions', style: 'min-width: 130px', exportable: false },
]);

const canShow = can('urgencytypes.show');
const canEdit = can('urgencytypes.edit');
const canDestroy = can('urgencytypes.destroy');
const canCreate = can('urgencytypes.create');

const headerLinks = [];
if (canCreate) {
  headerLinks.push({ to: 'urgencytypes.create', text: 'generics.new' });
}

const fetchHandler = async (params) => {
  return await UrgencyTypeService.list(params);
};

const deleteHandler = (record) => {
  defaultDeleteHandler(confirm, datatable, toast, () => UrgencyTypeService.del(record.id));
};

onMounted(async () => {
  if (props.toast) {
    toast.add(props.toast);
  }
});
</script>

<template>
  <AuthenticatedLayout :title="__('urgencytype.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('urgencytype.titles.entity_breadcrumb')"
      :breadcrumbs="[{ to: 'urgencytypes.index', text: __('urgencytype.titles.entity_breadcrumb') }, { text: __('generics.list') }]"
      :links="headerLinks"
    />

    <Datatable
      ref="datatable"
      :filters="filters"
      :fetchHandler="fetchHandler"
      sortField="name"
      :sortOrder="1"
      :columns="columns"
    >
      <template #filter-name="{ filterModel, filterCallback }">
        <InputText v-model="filterModel.value" @input="debouncedFilter(filterCallback)" fluid :placeholder="__('urgencytype.table.name.placeholder')" />
      </template>

      <template #filter-code="{ filterModel, filterCallback }">
        <InputText v-model="filterModel.value" @input="debouncedFilter(filterCallback)" fluid :placeholder="__('urgencytype.table.code.placeholder')" />
      </template>

      <template #filter-priority_weight="{ filterModel, filterCallback }">
        <InputText v-model="filterModel.value" @input="debouncedFilter(filterCallback)" fluid :placeholder="__('urgencytype.table.priority_weight.placeholder')" />
      </template>

      <template #filter-sla_hours="{ filterModel, filterCallback }">
        <InputText v-model="filterModel.value" @input="debouncedFilter(filterCallback)" fluid :placeholder="__('urgencytype.table.sla_hours.placeholder')" />
      </template>

      <template #body-actions="{ data }">
        <Link :href="route('urgencytypes.show', data.id)" v-if="canShow">
          <span class="material-symbols-rounded cursor-pointer transition-all text-slate-500 hover:text-sky-600">visibility</span>
        </Link>
        <Link :href="route('urgencytypes.edit', data.id)" v-if="canEdit">
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
