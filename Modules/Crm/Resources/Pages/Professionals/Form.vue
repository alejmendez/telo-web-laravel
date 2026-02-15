<script setup>
import CardSection from '@Core/Components/CardSection.vue';
import VInputDni from '@Core/Components/Form/VInputDni.vue';
import VInput from '@Core/Components/Form/VInput.vue';
import VSelect from '@Core/Components/Form/VSelect.vue';
import VSelectMultiple from '@Core/Components/Form/VSelectMultiple.vue';

import FormContact from '@Crm/Components/FormContact.vue';
import FormAddress from '@Crm/Components/FormAddress.vue';

const props = defineProps({
  form: Object,
  readOnly: Boolean,
  professional_types: Array,
  categories: Array,
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
      <VSelectMultiple
        id="categories"
        v-model="form.categories"
        filter
        :options="props.categories"
        :label="__('professional.form.categories.label')"
        :placeholder="__('generics.please_select')"
        :message="form.errors.categories"
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
