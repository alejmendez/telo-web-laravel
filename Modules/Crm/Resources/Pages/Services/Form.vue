<script setup>
import CardSection from '@Core/Components/CardSection.vue';
import VSelect from '@Core/Components/Form/VSelect.vue';
import VInput from '@Core/Components/Form/VInput.vue';

const props = defineProps({
  form: Object,
  readOnly: Boolean,
  requests: Array,
  professionals: Array,
  statuses: Array,
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
        id="request_id"
        v-model="form.request_id"
        filter
        classWrapper="col-span-2"
        :options="props.requests"
        :label="__('services.form.request_id.label')"
        :placeholder="__('generics.please_select')"
        :message="form.errors.request_id"
        :readonly="props.readOnly"
      />

      <VSelect
        id="professional_id"
        v-model="form.professional_id"
        filter
        :options="props.professionals"
        :label="__('services.form.professional_id.label')"
        :placeholder="__('generics.please_select')"
        :message="form.errors.professional_id"
        :readonly="props.readOnly"
      />

      <VSelect
        id="status"
        v-model="form.status"
        filter
        :options="props.statuses"
        :label="__('services.form.status.label')"
        :placeholder="__('generics.please_select')"
        :message="form.errors.status"
        :readonly="props.readOnly"
      />

      <VInput
        id="started_at"
        v-model="form.started_at"
        type="date"
        :label="__('services.form.started_at.label')"
        :placeholder="__('generics.please_select')"
        :message="form.errors.started_at"
        :readonly="props.readOnly"
      />

      <VInput
        id="completed_at"
        v-model="form.completed_at"
        type="date"
        :label="__('services.form.completed_at.label')"
        :placeholder="__('generics.please_select')"
        :message="form.errors.completed_at"
        :readonly="props.readOnly"
      />
    </CardSection>
  </form>
</template>
