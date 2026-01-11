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
        :label="__('location.form.name.label')"
        :message="form.errors.name"
        :readonly="props.readOnly"
      />
      <VInput
        id="type"
        v-model="form.type"
        :label="__('location.form.type.label')"
        :message="form.errors.type"
        :readonly="props.readOnly"
      />
      <VInput
        id="country_code"
        v-model="form.country_code"
        :label="__('location.form.country_code.label')"
        :message="form.errors.country_code"
        :readonly="props.readOnly"
      />
      <VInput
        id="parent_id"
        v-model="form.parent_id"
        :label="__('location.form.parent_id.label')"
        :message="form.errors.parent_id"
        :readonly="props.readOnly"
      />
      <VInput
        id="level"
        v-model="form.level"
        :label="__('location.form.level.label')"
        :message="form.errors.level"
        :readonly="props.readOnly"
        type="number"
      />
      <VInput
        id="code"
        v-model="form.code"
        :label="__('location.form.code.label')"
        :message="form.errors.code"
        :readonly="props.readOnly"
      />
    </CardSection>
  </form>
</template>
