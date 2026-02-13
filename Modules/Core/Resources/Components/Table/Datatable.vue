<script setup>
import { ref, computed, watch, onMounted, useAttrs } from 'vue';

import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import OverlayPanel from 'primevue/overlaypanel';
import Checkbox from 'primevue/checkbox';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import { useToast } from 'primevue/usetoast';
import { trans } from 'laravel-vue-i18n';

import { useSideBarStore } from '@Core/Stores/sidebar';

import Button from '@Core/Components/Form/Button.vue';

const toast = useToast();

const sideBarStore = useSideBarStore();

const props = defineProps({
  filters: Object,
  fetchHandler: Function,
  scrollHeight: {
    type: String,
    default: null,
  },
  columns: {
    type: Array,
    default: () => [],
  },
});

const attrs = useAttrs();

const filters = ref({ ...props.filters });

const loading = ref(true);

const initLazyParams = {};
if (attrs.sortField) {
  initLazyParams.sortField = attrs.sortField;
}

if (attrs.sortOrder) {
  initLazyParams.sortOrder = attrs.sortOrder;
}

const lazyParams = ref(initLazyParams);
const records = ref([]);
const metadataInitial = {
  total: 0,
  from: 1,
  to: 1,
  current_page: 1,
  last_page: 1,
  per_page: 10,
};
const metadata = ref(metadataInitial);

const toggleableColumns = computed(() => props.columns.filter((col) => col.header && !col.frozen));
const selectedColumns = ref([]);

const visibleColumns = computed(() => {
  if (props.columns.length === 0) return [];
  return props.columns.filter((col) => !col.header || col.frozen || selectedColumns.value.includes(col.field));
});

const initialized = ref(false);

watch(
  () => toggleableColumns.value,
  (val) => {
    if (!initialized.value && val.length > 0) {
      selectedColumns.value = val.map((col) => col.field);
      initialized.value = true;
    }
  },
  { immediate: true },
);

const getFieldValue = (data, field) => {
  if (!field) return null;
  if (field.includes('.')) {
    return field.split('.').reduce((obj, key) => obj?.[key], data);
  }
  return data[field];
};

const loadLazyData = async () => {
  loading.value = true;
  lazyParams.value = { ...lazyParams.value };

  const response = await props.fetchHandler(lazyParams.value);

  if (!response) {
    toast.add({
      severity: 'error',
      summary: trans('generics.tables.errors.could_not_load_the_data_summary'),
      detail: trans('generics.tables.errors.could_not_load_the_data'),
    });

    records.value = [];
    metadata.value = metadataInitial;
    loading.value = false;
    return;
  }

  records.value = response.data;
  metadata.value = response;
  loading.value = false;
};

defineExpose({ loadLazyData, records, metadata });

const onPage = (event) => {
  lazyParams.value = event;
  loadLazyData();
};

const onSort = (event) => {
  lazyParams.value = event;
  loadLazyData();
};

const onFilter = () => {
  lazyParams.value.filters = filters.value;
  loadLazyData();
};

const clearFilter = () => {
  Object.keys(filters.value).forEach(key => {
    const filter = filters.value[key];
    if (filter && typeof filter === 'object') {
      if ('constraints' in filter && Array.isArray(filter.constraints)) {
        filter.constraints.forEach(constraint => {
          constraint.value = null;
        });
      } else if ('value' in filter) {
        filter.value = null;
      } else {
        filters.value[key] = null;
      }
    } else {
      filters.value[key] = null;
    }
  });
  lazyParams.value.filters = {};
  loadLazyData();
};

const op = ref();

const toggleMenu = (event) => {
  op.value.toggle(event);
};

const allColumnsSelected = computed(() => {
  return toggleableColumns.value.length > 0 &&
         toggleableColumns.value.every(col => selectedColumns.value.includes(col.field));
});

const toggleAllColumns = () => {
  if (allColumnsSelected.value) {
    selectedColumns.value = [toggleableColumns.value[0].field];
  } else {
    selectedColumns.value = toggleableColumns.value.map(col => col.field);
  }
};

const toggleColumn = (field) => {
  if (selectedColumns.value.includes(field)) {
    selectedColumns.value = selectedColumns.value.filter(f => f !== field);
  } else {
    selectedColumns.value.push(field);
  }
};

onMounted(() => {
  loadLazyData();
});
</script>

<template>
  <div
    class="overflow-x-auto"
    :class="{ 'sm:w-[calc(100vw-65px)] md:w-full ': sideBarStore.show, 'sm:w-[calc(100vw-65px)] md:w-[calc(100vw-65px)] lg:w-[calc(100vw-380px)]': !sideBarStore.show }"
  >
    <DataTable
      class="w-full"
      lazy
      dataKey="id"
      filterDisplay="row"
      paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
      v-model:filters="filters"
      scrollable
      stripedRows
      tableStyle="min-width: 100px"
      v-bind="attrs"
      :scrollHeight="props.scrollHeight"
      :value="records"
      :totalRecords="metadata.total"
      :first="metadata.from"
      :last="metadata.to"
      :currentPage="metadata.current_page"
      :totalPages="metadata.last_page"
      :rows="metadata.per_page"
      :paginator="metadata.total > 0"
      :rowsPerPageOptions="[5, 10, 25, 50, 100]"
      :loading="loading"
      :currentPageReportTemplate="metadata.total > 0 ? __('generics.tables.pagination.template', {
        from: metadata.from || 1,
        to: metadata.to || 1,
        total: metadata.total || 1
      }) : ''"
      @page="onPage($event)"
      @sort="onSort($event)"
    >
      <template #header>
        <div class="flex justify-between">
          <div class="flex items-center gap-2">
            <div class="sm:hidden md:block">
              <Button
                type="button"
                icon="pi pi-filter-slash"
                label="Limpiar"
                @click="clearFilter"
              />
            </div>
            <div v-if="toggleableColumns.length > 0">
              <Button
                type="button"
                icon="pi pi-ellipsis-v"
                class="!p-2 !rounded-full !w-10 !h-10 flex items-center justify-center text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800"
                text
                rounded
                @click="toggleMenu"
              />
              <OverlayPanel ref="op">
                <div class="flex flex-col w-64">
                  <div
                    class="flex items-center p-2 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer transition-colors rounded-md mb-1"
                    @click="toggleAllColumns"
                  >
                    <Checkbox :modelValue="allColumnsSelected" binary readonly class="pointer-events-none" />
                    <span class="ml-2 font-medium">
                      {{ allColumnsSelected ? 'Ocultar todas' : 'Mostrar todas' }}
                    </span>
                  </div>
                  <div class="h-px bg-slate-200 dark:bg-slate-700 my-1"></div>
                  <div class="max-h-64 overflow-y-auto">
                    <div
                      v-for="col in toggleableColumns"
                      :key="col.field"
                      class="flex items-center p-2 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer transition-colors rounded-md"
                      @click="toggleColumn(col.field)"
                    >
                      <Checkbox :modelValue="selectedColumns.includes(col.field)" binary readonly class="pointer-events-none" />
                      <span class="ml-2 truncate" :title="col.header">{{ col.header }}</span>
                    </div>
                  </div>
                </div>
              </OverlayPanel>
            </div>
          </div>
          <IconField>
            <InputIcon>
              <i class="pi pi-search" />
            </InputIcon>
            <InputText v-model="filters['global'].value" :placeholder="__('generics.tables.search') + '...'" @keyup.enter="onFilter" />
          </IconField>
        </div>
      </template>

      <template #empty> {{ __('generics.tables.empty') }} </template>

      <Column
        v-for="(col, index) in visibleColumns"
        :key="col.field || index"
        v-bind="col"
        :field="col.field"
        :showFilterMenu="false"
        :filterField="col.field">
        <template #body="slotProps">
          <slot :name="'body-' + (col.field || col.type)" :data="slotProps.data" :index="slotProps.index">
            {{ getFieldValue(slotProps.data, col.field) }}
          </slot>
        </template>
        <template #filter="slotProps">
          <slot
            :name="'filter-' + (col.field || col.type)"
            :filterModel="filters[col.field]"
            :filterCallback="onFilter"
          >
          </slot>
        </template>
      </Column>

      <slot></slot>
    </DataTable>
  </div>
</template>
