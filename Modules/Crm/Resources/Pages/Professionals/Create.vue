<script setup>
import { useForm } from '@inertiajs/vue3';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import FormProfessional from '@Crm/Pages/Professionals/Form.vue';

const props = defineProps({
  professional_types: Array,
  locations: Array,
});

const form = useForm({
  dni: null,
  first_name: null,
  last_name: null,
  email: null,
  phone_e164: null,
  location_id: null,
  professional_type_id: null,
  bio: null,
});

const submitHandler = () => form.post(route('professionals.store'));
</script>

<template>
  <AuthenticatedLayout :title="__('professional.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('professional.titles.create')"
      :breadcrumbs="[{ to: 'professionals.index', text: __('professional.titles.entity_breadcrumb') }, { text: __('generics.actions.create') }]"
      :form="{ instance: form, submitHandler, submitText: __('generics.buttons.create'), hrefCancel: route('professionals.index') }"
    />
    <FormProfessional
      :form="form"
      :submitHandler="submitHandler"
      :professional_types="props.professional_types"
      :locations="props.locations"
    />
  </AuthenticatedLayout>
</template>
