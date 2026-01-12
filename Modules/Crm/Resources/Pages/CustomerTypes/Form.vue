<script setup>
import CardSection from '@Core/Components/CardSection.vue';
import VInput from '@Core/Components/Form/VInput.vue';

const props = defineProps({
  form: Object,
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
      <VInput
        id="name"
        v-model="form.name"
        :label="__('customertype.form.name.label')"
        :message="form.errors.name"
        :readonly="props.readOnly"
      />
      <VInput
        id="code"
        v-model="form.code"
        :label="__('customertype.form.code.label')"
        :message="form.errors.code"
        :readonly="props.readOnly"
      />
    </CardSection>
  </form>
</template>
