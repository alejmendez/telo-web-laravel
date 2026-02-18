<script setup>
import { ref, computed, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from 'primevue/useconfirm';
import { trans } from 'laravel-vue-i18n';

import { FilterMatchMode } from '@primevue/core/api';
import InputText from 'primevue/inputtext';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import Datatable from '@Core/Components/Table/Datatable.vue';
import CustomerService from '@Crm/Services/CustomerService.js';
import { defaultDeleteHandler, debouncedFilter } from '@Core/Utils/table.js';
import { idFormater } from '@Core/Utils/format.js';

import { can } from '@Auth/Services/Auth';

const props = defineProps({
  toast: Object,
});

const toast = useToast();
const confirm = useConfirm();

const datatable = ref(null);

const filters = {
  global: { value: null, matchMode: FilterMatchMode.CONTAINS },
  id: { value: null, matchMode: FilterMatchMode.CONTAINS },
  dni: { value: null, matchMode: FilterMatchMode.CONTAINS },
  full_name: { value: null, matchMode: FilterMatchMode.CONTAINS },
  email: { value: null, matchMode: FilterMatchMode.CONTAINS },
  phone_e164: { value: null, matchMode: FilterMatchMode.CONTAINS },
};

const columns = computed(() => [
  { field: 'id', header: trans('customer.table.id.label'), sortable: true, style: 'min-width: 200px' },
  { field: 'dni', header: trans('customer.table.dni.label'), sortable: true, style: 'min-width: 200px' },
  { field: 'full_name', header: trans('customer.table.full_name.label'), sortable: true, style: 'min-width: 200px' },
  { field: 'email', header: trans('customer.table.email.label'), sortable: false, style: 'min-width: 200px' },
  { field: 'phone_e164', header: trans('customer.table.phone_e164.label'), sortable: false, style: 'min-width: 200px' },
  { type: 'actions', style: 'min-width: 130px', exportable: false },
]);

const canShow = can('customers.show');
const canEdit = can('customers.edit');
const canDestroy = can('customers.destroy');
const canCreate = can('customers.create');

const headerLinks = [];
if (canCreate) {
  headerLinks.push({ to: 'customers.create', text: 'generics.new' });
}

const fetchHandler = async (params) => {
  return await CustomerService.list(params);
};

const deleteHandler = (record) => {
  defaultDeleteHandler(confirm, datatable, toast, () => CustomerService.del(record.id));
};

onMounted(async () => {
  if (props.toast) {
    toast.add(props.toast);
  }
});
</script>

<template>
  <AuthenticatedLayout :title="__('customer.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('customer.titles.entity_breadcrumb')"
      :breadcrumbs="[{ to: 'customers.index', text: __('customer.titles.entity_breadcrumb') }, { text: __('generics.list') }]"
      :links="headerLinks"
    />

    <Datatable
      ref="datatable"
      :filters="filters"
      :fetchHandler="fetchHandler"
      sortField="full_name"
      :sortOrder="1"
      :columns="columns"
    >
      <template #filter-id="{ filterModel, filterCallback }">
        <InputText v-model="filterModel.value" @input="debouncedFilter(filterCallback)" fluid :placeholder="__('professional.table.id.placeholder')" />
      </template>

      <template #filter-dni="{ filterModel, filterCallback }">
        <InputText v-model="filterModel.value" @input="debouncedFilter(filterCallback)" fluid :placeholder="__('customer.table.dni.placeholder')" />
      </template>

      <template #filter-full_name="{ filterModel, filterCallback }">
        <InputText v-model="filterModel.value" @input="debouncedFilter(filterCallback)" fluid :placeholder="__('customer.table.full_name.placeholder')" />
      </template>

      <template #body-id="{ data }">
        {{ idFormater(data.id, 'TU') }}
      </template>

      <template #body-email="{ data }">
        {{ data.email.join(', ') }}
      </template>

      <template #body-phone_e164="{ data }">
        {{ data.phone_e164.join(', ') }}
      </template>

      <template #body-actions="{ data }">
        <Link :href="route('customers.show', data.id)" v-if="canShow">
          <span class="material-symbols-rounded cursor-pointer transition-all text-slate-500 hover:text-sky-600">visibility</span>
        </Link>
        <Link :href="route('customers.edit', data.id)" v-if="canEdit">
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
