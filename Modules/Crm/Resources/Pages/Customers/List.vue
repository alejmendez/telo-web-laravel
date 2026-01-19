<script setup>
import { ref, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from 'primevue/useconfirm';

import { FilterMatchMode, FilterOperator } from '@primevue/core/api';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import Datatable from '@Core/Components/Table/Datatable.vue';
import CustomerService from '@Crm/Services/CustomerService.js';
import { defaultDeleteHandler } from '@Core/Utils/table.js';

import { can } from '@Auth/Services/Auth';

const props = defineProps({
  toast: Object,
});

const toast = useToast();
const confirm = useConfirm();

const datatable = ref(null);

const filters = {
  global: { value: null, matchMode: FilterMatchMode.CONTAINS },
  dni: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.CONTAINS }] },
  full_name: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.CONTAINS }] },
  email: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.CONTAINS }] },
  phone_e164: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.CONTAINS }] },
};

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
    >
      <Column field="dni" :header="__('customer.table.dni.label')" sortable frozen style="min-width: 200px">
        <template #body="{ data }">
          {{ data.dni }}
        </template>
        <template #filter="{ filterModel }">
          <InputText v-model="filterModel.value" type="text" :placeholder="__('customer.table.dni.placeholder')" />
        </template>
      </Column>

      <Column field="full_name" :header="__('customer.table.full_name.label')" sortable frozen style="min-width: 200px">
        <template #body="{ data }">
          {{ data.full_name }}
        </template>
        <template #filter="{ filterModel }">
          <InputText v-model="filterModel.value" type="text" :placeholder="__('customer.table.full_name.placeholder')" />
        </template>
      </Column>

      <Column field="email" :header="__('customer.table.email.label')" sortable style="min-width: 200px">
        <template #body="{ data }">
          {{ data.email }}
        </template>
        <template #filter="{ filterModel }">
          <InputText v-model="filterModel.value" type="text" :placeholder="__('customer.table.email.placeholder')" />
        </template>
      </Column>

      <Column field="phone_e164" :header="__('customer.table.phone_e164.label')" sortable style="min-width: 200px">
        <template #body="{ data }">
          {{ data.phone_e164 }}
        </template>
        <template #filter="{ filterModel }">
          <InputText v-model="filterModel.value" type="text" :placeholder="__('customer.table.phone_e164.placeholder')" />
        </template>
      </Column>


      <Column :exportable="false" style="max-width: 130px">
        <template #body="slotProps">
          <Link :href="route('customers.show', slotProps.data.id)" v-if="canShow">
            <span class="material-symbols-rounded cursor-pointer transition-all text-slate-500 hover:text-sky-600">visibility</span>
          </Link>
          <Link :href="route('customers.edit', slotProps.data.id)" v-if="canEdit">
            <span class="material-symbols-rounded cursor-pointer transition-all text-slate-500 hover:text-emerald-600">edit</span>
          </Link>
          <span
            class="material-symbols-rounded cursor-pointer transition-all text-slate-500 hover:text-pink-600"
            @click="deleteHandler(slotProps.data)"
            v-if="canDestroy"
          >
            delete
          </span>
        </template>
      </Column>
    </Datatable>
  </AuthenticatedLayout>
</template>
