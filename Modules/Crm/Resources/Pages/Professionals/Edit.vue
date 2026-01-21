<script setup>
import { useForm } from '@inertiajs/vue3';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import FormProfessional from '@Crm/Pages/Professionals/Form.vue';

const props = defineProps({
  data: Object,
  professional_types: Array,
  locations: Array,
});

const { data } = props.data;

const form = useForm({
  _method: 'PATCH',
  id: data.id,
  dni: data.dni,
  first_name: data.first_name,
  last_name: data.last_name,
  email: data.email,
  phone_e164: data.phone_e164,
  professional_type_id: data.professional_type_id,
  location_id: data.location_id,
  bio: data.bio,
});

const submitHandler = () => form.post(route('professionals.update', data.id));
</script>

<template>
  <AuthenticatedLayout :title="__('professional.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('professional.titles.edit')"
      :breadcrumbs="[{ to: 'professionals.index', text: __('professional.titles.entity_breadcrumb') }, { text: __('generics.actions.edit') }]"
      :form="{ instance: form, submitHandler, submitText: __('generics.buttons.save_edit'), hrefCancel: route('professionals.index') }"
    />
    <FormProfessional
      :form="form"
      :submitHandler="submitHandler"
      :professional_types="props.professional_types"
      :locations="props.locations"
    />
  </AuthenticatedLayout>
</template>
