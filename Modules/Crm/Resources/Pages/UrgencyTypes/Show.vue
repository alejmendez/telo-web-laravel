<script setup>
import { useForm } from '@inertiajs/vue3';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import FormUrgencyType from '@Crm/Pages/UrgencyTypes/Form.vue';

const props = defineProps({
  data: Object,
});

const { data } = props.data;

const form = useForm({
  id: data.id,
  name: data.name,
  code: data.code,
  priority_weight: data.priority_weight,
  sla_hours: data.sla_hours,
});

const submitHandler = () => {};
</script>

<template>
  <AuthenticatedLayout :title="__('urgencytype.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('urgencytype.titles.show')"
      :breadcrumbs="[{ to: 'urgencytypes.index', text: __('urgencytype.titles.entity_breadcrumb') }, { text: __('generics.actions.show') }]"
      :links="[{ to: route('urgencytypes.index'), text: __('generics.buttons.back') }]"
    />
    <FormUrgencyType
      :form="form"
      :readOnly="true"
      :submitHandler="submitHandler"
    />
  </AuthenticatedLayout>
</template>
