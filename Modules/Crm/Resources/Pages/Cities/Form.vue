<script setup>
import CardSection from '@Core/Components/CardSection.vue';
import VInput from '@Core/Components/Form/VInput.vue';
import VSelect from '@Core/Components/Form/VSelect.vue';

const props = defineProps({
  form: Object,
  countries: Array,
  readOnly: Boolean,
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
      <VSelect
        id="country_id"
        v-model="form.country_id"
        :label="__('city.form.country_id.label')"
        :placeholder="__('generics.please_select')"
        :message="form.errors.country_id"
        :readonly="props.readOnly"
        :options="props.countries"
      />
    </CardSection>
    <CardSection>
      <VInput
        id="name"
        v-model="form.name"
        :label="__('city.form.name.label')"
        :message="form.errors.name"
        :readonly="props.readOnly"
      />
    </CardSection>
  </form>
</template>
