<script setup>
import { useForm } from '@inertiajs/vue3';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import FormProfessional from '@Crm/Pages/Professionals/Form.vue';

const props = defineProps({
  data: Object,
  professional_types: Array,
  locations: Array,
  categories: Array,
  contact_types: Array,
});

const selectValueFromArray = (array, value) => {
  return array.find(item => item.value === value);
}

const { data } = props.data;

const contacts = data.contacts?.length
  ? data.contacts.map(({ id, contact_type, content }) => ({
      id: id,
      contact_type: selectValueFromArray(props.contact_types, contact_type),
      content,
    }))
  : [{ id: null, contact_type: null, content: null }];

const addresses = data.addresses?.length
  ? data.addresses.map(({ id, location_id, address, postal_code, is_primary }) => ({
      id: id,
      location_id: {
        [location_id]: true
      },
      address,
      postal_code,
      is_primary,
    }))
  : [{ id: null, location_id: null, address: null, postal_code: null, is_primary: true }];

const form = useForm({
  _method: 'PATCH',
  id: data.id,
  dni: data.dni,
  first_name: data.first_name,
  last_name: data.last_name,
  professional_type_id: props.professional_types.find((t) => t.value === data.professional_type_id),
  categories: props.categories.filter((c) => data.categories.includes(c.value)),
  bio: data.bio,
  contacts: contacts,
  addresses: addresses,
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
      :categories="props.categories"
      :contact_types="props.contact_types"
    />
  </AuthenticatedLayout>
</template>
