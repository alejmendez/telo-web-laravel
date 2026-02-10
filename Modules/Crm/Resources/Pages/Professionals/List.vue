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
import ProfessionalService from '@Crm/Services/ProfessionalService.js';
import { defaultDeleteHandler } from '@Core/Utils/table.js';

import { can } from '@Auth/Services/Auth';

const props = defineProps({
  toast: Object,
  professional_types: Array,
  locations: Array,
});

const toast = useToast();
const confirm = useConfirm();

const datatable = ref(null);

const filters = {
  global: { value: null, matchMode: FilterMatchMode.CONTAINS },
  full_name: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.CONTAINS }] },
  email: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.CONTAINS }] },
  dni: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.CONTAINS }] },
};

const canShow = can('professionals.show');
const canEdit = can('professionals.edit');
const canDestroy = can('professionals.destroy');
const canCreate = can('professionals.create');

const headerLinks = [];
if (canCreate) {
  headerLinks.push({ to: 'professionals.create', text: 'generics.new' });
}

const fetchHandler = async (params) => {
  return await ProfessionalService.list(params);
};

const deleteHandler = (record) => {
  defaultDeleteHandler(confirm, datatable, toast, () => ProfessionalService.del(record.id));
};

onMounted(async () => {
  if (props.toast) {
    toast.add(props.toast);
  }
});
</script>

<template>
  <AuthenticatedLayout :title="__('professional.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('professional.titles.entity_breadcrumb')"
      :breadcrumbs="[{ to: 'professionals.index', text: __('professional.titles.entity_breadcrumb') }, { text: __('generics.list') }]"
      :links="headerLinks"
    />

    <Datatable
      ref="datatable"
      :filters="filters"
      :fetchHandler="fetchHandler"
      sortField="first_name"
      :sortOrder="1"
    >
      <Column field="dni.label" :header="__('professional.table.dni.label')" sortable frozen style="min-width: 200px">
        <template #body="{ data }">
          {{ data.dni }}
        </template>
        <template #filter="{ filterModel }">
          <InputText v-model="filterModel.value" type="text" :placeholder="__('professional.form.dni.placeholder')" />
        </template>
      </Column>
      <Column field="full_name.label" :header="__('professional.table.full_name.label')" sortable frozen style="min-width: 200px">
        <template #body="{ data }">
          {{ data.full_name }}
        </template>
        <template #filter="{ filterModel }">
          <InputText v-model="filterModel.value" type="text" :placeholder="__('professional.form.full_name.placeholder')" />
        </template>
      </Column>
      <Column field="email" :header="__('professional.table.email.label')" sortable style="min-width: 200px">
        <template #body="{ data }">
          <div v-for="email in data.email">
            {{ email }}
          </div>
        </template>
        <template #filter="{ filterModel }">
          <InputText v-model="filterModel.value" type="text" :placeholder="__('professional.form.email.placeholder')" />
        </template>
      </Column>
      <Column field="phone_e164" :header="__('professional.table.phone_e164.label')" sortable style="min-width: 150px">
        <template #body="{ data }">
          <div v-for="phone_e164 in data.phone_e164">
            {{ phone_e164 }}
          </div>
        </template>
        <template #filter="{ filterModel }">
          <InputText v-model="filterModel.value" type="text" :placeholder="__('professional.form.phone_e164.placeholder')" />
        </template>
      </Column>

      <Column :exportable="false" style="max-width: 130px">
        <template #body="slotProps">
          <Link :href="route('professionals.show', slotProps.data.id)" v-if="canShow">
            <span class="material-symbols-rounded cursor-pointer transition-all text-slate-500 hover:text-sky-600">visibility</span>
          </Link>
          <Link :href="route('professionals.edit', slotProps.data.id)" v-if="canEdit">
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
