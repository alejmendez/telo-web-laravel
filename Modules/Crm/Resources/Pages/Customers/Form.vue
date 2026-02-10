<script setup>
import CardSection from '@Core/Components/CardSection.vue';
import VInputDni from '@Core/Components/Form/VInputDni.vue';
import VInput from '@Core/Components/Form/VInput.vue';
import VSelect from '@Core/Components/Form/VSelect.vue';

import FormContact from '@Crm/Components/FormContact.vue';
import FormAddress from '@Crm/Components/FormAddress.vue';

const props = defineProps({
  form: Object,
  readOnly: Boolean,
  customer_types: Array,
  locations: Array,
  contact_types: Array,
  submitHandler: {
    type: Function,
    default: false,
  },
});

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
      <VSelect
        id="customer_type_id"
        v-model="form.customer_type_id"
        filter
        :options="props.customer_types"
        :label="__('customer.form.customer_type_id.label')"
        :placeholder="__('generics.please_select')"
        :message="form.errors.customer_type_id"
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

    <FormContact
      :form="form"
      :contact_types="props.contact_types"
      :readOnly="props.readOnly"
    />

    <FormAddress
      :form="form"
      :locations="props.locations"
      :readOnly="props.readOnly"
    />
  </form>
</template>
