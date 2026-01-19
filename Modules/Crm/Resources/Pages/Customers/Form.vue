<script setup>
import CardSection from '@Core/Components/CardSection.vue';
import VInputDni from '@Core/Components/Form/VInputDni.vue';
import VInput from '@Core/Components/Form/VInput.vue';
import VSelect from '@Core/Components/Form/VSelect.vue';
import VInputLocation from '@Core/Components/Form/VInputLocation.vue';

import LocationService from '@Crm/Services/LocationService.js';

const props = defineProps({
  form: Object,
  readOnly: Boolean,
  customer_types: Array,
  dni_locations: Array,
  submitHandler: {
    type: Function,
    default: false,
  },
});

const locations_nodes = LocationService.createNodes(props.dni_locations);
const form = props.form;
</script>

<template>
  <form @submit.prevent="props.submitHandler">
    <CardSection>
      <VInputDni
        id="dni"
        v-model="form.dni"
        classWrapper="col-span-2"
        :label="__('customer.form.dni.label')"
        :message="form.errors.dni"
        :readonly="props.readOnly"
      />
      <VInput
        id="first_name"
        v-model="form.first_name"
        :label="__('customer.form.first_name.label')"
        :message="form.errors.first_name"
        :readonly="props.readOnly"
      />
      <VInput
        id="last_name"
        v-model="form.last_name"
        :label="__('customer.form.last_name.label')"
        :message="form.errors.last_name"
        :readonly="props.readOnly"
      />
      <VInput
        id="email"
        v-model="form.email"
        :label="__('customer.form.email.label')"
        :message="form.errors.email"
        :readonly="props.readOnly"
      />
      <VInput
        id="phone_e164"
        v-model="form.phone_e164"
        type="tel"
        :label="__('customer.form.phone_e164.label')"
        :message="form.errors.phone_e164"
        :readonly="props.readOnly"
      />
      <VSelect
        id="customer_type_id"
        v-model="form.customer_type_id"
        :options="props.customer_types"
        :label="__('customer.form.customer_type_id.label')"
        :placeholder="__('generics.please_select')"
        :message="form.errors.customer_type_id"
        :readonly="props.readOnly"
      />
      <VInputLocation
        id="dni_location_id"
        v-model="form.dni_location_id"
        filter
        filterMode="lenient"
        :options="locations_nodes"
        :label="__('customer.form.dni_location_id.label')"
        :placeholder="__('generics.please_select')"
        :message="form.errors.dni_location_id"
        :readonly="props.readOnly"
      />
      <VInput
        id="notes"
        v-model="form.notes"
        type="textarea"
        classWrapper="col-span-2"
        maxlength="800"
        :label="__('customer.form.notes.label')"
        :message="form.errors.notes"
        :readonly="props.readOnly"
      />
    </CardSection>
  </form>
</template>
