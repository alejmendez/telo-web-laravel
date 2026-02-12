<script setup>
import { ref, computed, onMounted } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from 'primevue/useconfirm';
import { trans } from 'laravel-vue-i18n';

import { FilterMatchMode } from '@primevue/core/api';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';

import slugify from '@Core/Utils/slugify';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import Datatable from '@Core/Components/Table/Datatable.vue';
import UserService from '@Users/Services/UserService.js';
import { deleteRowDatatable, debouncedFilter } from '@Core/Utils/table.js';
import { can } from '@Auth/Services/Auth';

const props = defineProps({
  toast: Object,
  roles: Array,
});

const toast = useToast();
const confirm = useConfirm();

const page = usePage();
const current_user_id = page.props.auth.user.id;

const datatable = ref(null);
const filter_role_options = ref(props.roles);

const filters = {
  global: { value: null, matchMode: FilterMatchMode.CONTAINS },
  full_name: { value: null, matchMode: FilterMatchMode.CONTAINS },
  dni: { value: null, matchMode: FilterMatchMode.CONTAINS },
  phone: { value: null, matchMode: FilterMatchMode.CONTAINS },
  'roles.name': { value: null, matchMode: FilterMatchMode.EQUALS },
  email: { value: null, matchMode: FilterMatchMode.CONTAINS },
};

const columns = computed(() => [
  { field: 'full_name', header: trans('user.table.full_name.label'), sortable: true, style: 'min-width: 200px' },
  { field: 'dni', header: trans('user.table.dni.label'), sortable: true, style: 'min-width: 200px' },
  { field: 'phone', header: trans('user.table.phone.label'), sortable: true, style: 'min-width: 200px' },
  { field: 'roles.name', header: trans('user.table.roles_name.label'), sortable: true, style: 'min-width: 200px' },
  { field: 'email', header: trans('user.table.email.label'), sortable: true, style: 'min-width: 200px' },
  { type: 'actions', style: 'min-width: 130px', exportable: false },
]);

const canShow = can('users.show');
const canEdit = can('users.edit');
const canDestroy = can('users.destroy');

const headerLinks = [];
if (can('users.create')) {
  headerLinks.push({ to: 'users.create', text: 'generics.new' });
}

const fetchHandler = async (params) => {
  return await UserService.list(params);
};

const deleteHandler = async (record) => {
  const options = {
    datatable,
    confirm,
    toast,
    handler: () => UserService.del(record.id),
  };

  deleteRowDatatable(options);
};

onMounted(async () => {
  if (props.toast) {
    toast.add(props.toast);
  }
});
</script>

<template>
  <AuthenticatedLayout :title="__('user.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('user.titles.entity_breadcrumb')"
      :breadcrumbs="[{ to: 'users.index', text: __('user.titles.entity_breadcrumb') }, { text: __('generics.list') }]"
      :links="headerLinks"
    />

    <Datatable
      ref="datatable"
      sortField="full_name"
      :filters="filters"
      :fetchHandler="fetchHandler"
      :sortOrder="1"
      :columns="columns"
    >
      <template #filter-full_name="{ filterModel, filterCallback }">
        <InputText v-model="filterModel.value" @input="debouncedFilter(filterCallback)" :placeholder="__('user.table.full_name.placeholder')" />
      </template>
      <template #filter-dni="{ filterModel, filterCallback }">
        <InputText v-model="filterModel.value" @input="debouncedFilter(filterCallback)" :placeholder="__('user.table.dni.placeholder')" />
      </template>
      <template #filter-phone="{ filterModel, filterCallback }">
        <InputText v-model="filterModel.value" @input="debouncedFilter(filterCallback)" :placeholder="__('user.table.phone.placeholder')" />
      </template>
      <template #filter-roles.name="{ filterModel, filterCallback }">
        <Select v-model="filterModel.value" :options="filter_role_options" optionLabel="text" optionValue="value" showClear @change="filterCallback()" :placeholder="__('user.table.roles_name.placeholder')" />
      </template>
      <template #filter-email="{ filterModel, filterCallback }">
        <InputText v-model="filterModel.value" @input="debouncedFilter(filterCallback)" :placeholder="__('user.table.email.placeholder')" />
      </template>

      <template #body-roles.name="{ data }">
        <span
          v-for="role in data.roles"
          class="inline-flex items-center justify-center px-2 py-1 mr-2 text-xs font-bold leading-none border rounded-full role"
          :class="slugify(role.name)"
        >
          {{ role.name }}
        </span>
      </template>

      <template #body-actions="{ data }">
        <Link :href="route('users.show', data.id)" v-if="canShow">
          <span class="material-symbols-rounded cursor-pointer transition-all text-slate-500 hover:text-sky-600">visibility</span>
        </Link>
        <Link :href="route('users.edit', data.id)" v-if="canEdit">
          <span class="material-symbols-rounded cursor-pointer transition-all text-slate-500 hover:text-emerald-600">edit</span>
        </Link>
        <span
          class="material-symbols-rounded cursor-pointer transition-all text-slate-500 hover:text-pink-600"
          @click="deleteHandler(data)"
          v-if="canDestroy && data.id !== current_user_id"
        >
          delete
        </span>
      </template>
    </Datatable>
  </AuthenticatedLayout>
</template>
