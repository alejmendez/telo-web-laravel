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
  submitHandler: {
    type: Function,
    default: false,
  },
  professional_types: Array,
  locations: Array,
});

const locations_nodes = LocationService.createNodes(props.locations);
const form = props.form;
</script>

<template>
  <form @submit.prevent="props.submitHandler">
    <CardSection>
      <VInputDni
        id="dni"
        v-model="form.dni"
        classWrapper="col-span-2"
        :label="__('professional.form.dni.label')"
        :message="form.errors.dni"
        :readonly="props.readOnly"
      />
      <VInput
        id="first_name"
        v-model="form.first_name"
        :label="__('professional.form.first_name.label')"
        :message="form.errors.first_name"
        :readonly="props.readOnly"
      />
      <VInput
        id="last_name"
        v-model="form.last_name"
        :label="__('professional.form.last_name.label')"
        :message="form.errors.last_name"
        :readonly="props.readOnly"
      />
      <VInput
        id="email"
        v-model="form.email"
        :label="__('professional.form.email.label')"
        :message="form.errors.email"
        :readonly="props.readOnly"
      />
      <VInput
        id="phone_e164"
        v-model="form.phone_e164"
        type="tel"
        :label="__('professional.form.phone_e164.label')"
        :message="form.errors.phone_e164"
        :readonly="props.readOnly"
      />
      <VSelect
        id="professional_type_id"
        v-model="form.professional_type_id"
        filter
        :options="props.professional_types"
        :label="__('professional.form.professional_type_id.label')"
        :placeholder="__('generics.please_select')"
        :message="form.errors.professional_type_id"
        :readonly="props.readOnly"
      />
      <VInputLocation
        id="location_id"
        v-model="form.location_id"
        filter
        filterMode="lenient"
        :options="locations_nodes"
        :label="__('professional.form.location_id.label')"
        :placeholder="__('generics.please_select')"
        :message="form.errors.location_id"
        :readonly="props.readOnly"
      />
      <VInput
        id="bio"
        v-model="form.bio"
        type="textarea"
        classWrapper="col-span-2"
        maxlength="800"
        :label="__('professional.form.bio.label')"
        :message="form.errors.bio"
        :readonly="props.readOnly"
      />
    </CardSection>
  </form>
</template>
