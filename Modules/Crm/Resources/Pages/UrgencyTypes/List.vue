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
import UrgencyTypeService from '@Crm/Services/UrgencyTypeService.js';
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
  name: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.CONTAINS }] },
  code: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.CONTAINS }] },
  priority_weight: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.CONTAINS }] },
  sla_hours: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.CONTAINS }] },
};

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
    >
      <Column field="name" :header="__('urgencytype.table.name')" sortable frozen style="min-width: 200px">
        <template #body="{ data }">
          {{ data.name }}
        </template>
        <template #filter="{ filterModel }">
          <InputText v-model="filterModel.value" type="text" placeholder="Buscar por nombre" />
        </template>
      </Column>

      <Column field="code" :header="__('urgencytype.table.code')" sortable style="min-width: 200px">
        <template #body="{ data }">
          {{ data.code }}
        </template>
        <template #filter="{ filterModel }">
          <InputText v-model="filterModel.value" type="text" placeholder="Buscar por código" />
        </template>
      </Column>

      <Column field="priority_weight" :header="__('urgencytype.table.priority_weight')" sortable style="min-width: 130px">
        <template #body="{ data }">
          {{ data.priority_weight }}
        </template>
        <template #filter="{ filterModel }">
          <InputText v-model="filterModel.value" type="number" placeholder="Buscar por peso de prioridad" />
        </template>
      </Column>

      <Column field="sla_hours" :header="__('urgencytype.table.sla_hours')" sortable style="min-width: 130px">
        <template #body="{ data }">
          {{ data.sla_hours }}
        </template>
        <template #filter="{ filterModel }">
          <InputText v-model="filterModel.value" type="number" placeholder="Buscar por horas SLA" />
        </template>
      </Column>

      <Column :exportable="false" style="max-width: 130px">
        <template #body="slotProps">
          <Link :href="route('urgencytypes.show', slotProps.data.id)" v-if="canShow">
            <span class="material-symbols-rounded cursor-pointer transition-all text-slate-500 hover:text-sky-600">visibility</span>
          </Link>
          <Link :href="route('urgencytypes.edit', slotProps.data.id)" v-if="canEdit">
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
