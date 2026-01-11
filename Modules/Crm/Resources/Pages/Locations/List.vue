<script setup>
import { ref, watch, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from 'primevue/useconfirm';

import Tree from 'primevue/tree';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import LocationService from '@Crm/Services/LocationService.js';
import LocationForm from '@Crm/Pages/Locations/Form.vue';

import { can } from '@Auth/Services/Auth';

const props = defineProps({
  toast: Object,
  nodes: Array,
});

const toast = useToast();
const confirm = useConfirm();

const canShow = can('locations.show');
const canEdit = can('locations.edit');
const canDestroy = can('locations.destroy');
const canCreate = can('locations.create');

const node_transformated = LocationService.createNodes(props.nodes);

const nodes = ref(node_transformated);
const selectedKey = ref(null);

const selectedNode = ref(null);

watch(selectedKey, (newValue) => {
  const data = LocationService.getDataNode(nodes.value, newValue);

  if (data) {
    selectedNode.value = data;

    Object.keys(data).forEach((item) => {
      form[item] = data[item];
    });
  }
});

const form = useForm({
  name: null,
  type: null,
  country_code: null,
  parent_id: null,
  level: null,
  code: null,
});

const headerLinks = [];
if (canCreate) {
  headerLinks.push({ to: 'locations.create', text: 'generics.new' });
}

const fetchHandler = async (params) => {
  return await LocationService.list(params);
};

const submitHandler = (form) => {
  // form.post(route('locations.store'));
};

onMounted(async () => {
  if (props.toast) {
    toast.add(props.toast);
  }
});
</script>

<template>
  <AuthenticatedLayout :title="__('location.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('location.titles.entity_breadcrumb')"
      :breadcrumbs="[{ to: 'locations.index', text: __('location.titles.entity_breadcrumb') }, { text: __('generics.list') }]"
      :links="headerLinks"
    />

    <div class="grid md:grid-cols-2 gap-2 sm:grid-cols-1">
      <div class="card-section mt-5 rounded-xl shadow-sm ring-1 ring-gray-950/5 w-full">
        <Tree
          :value="nodes"
          v-model:selectionKeys="selectedKey"
          selectionMode="single"
          class="w-full"
        ></Tree>
      </div>
      <div v-if="selectedKey">
        <LocationForm
          :form="form"
          :readOnly="!canEdit"
          :submitHandler="submitHandler"
        />
      </div>
    </div>
  </AuthenticatedLayout>
</template>
