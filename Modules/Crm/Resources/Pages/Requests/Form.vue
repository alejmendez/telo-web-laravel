<script setup>
import CardSection from '@Core/Components/CardSection.vue';
import VInput from '@Core/Components/Form/VInput.vue';
import VSelect from '@Core/Components/Form/VSelect.vue';
import VInputDate from '@Core/Components/Form/VInputDate.vue';

const props = defineProps({
  form: Object,
  readOnly: Boolean,
  customers: Array,
  categories: Array,
  urgency_types: Array,
  professionals: Array,
  submitHandler: {
    type: Function,
    default: false,
  },
});

const form = props.form;

const statusOptions = [
  { value: 'pending', text: 'Pending' },
  { value: 'active', text: 'Active' },
  { value: 'rejected', text: 'Rejected' },
];
</script>

<template>
  <form @submit.prevent="props.submitHandler">
    <CardSection>
      <VSelect
        id="customer_id"
        v-model="form.customer_id"
        filter
        :options="props.customers"
        :label="__('requests.form.customer_id.label')"
        :placeholder="__('generics.please_select')"
        :message="form.errors.customer_id"
        :readonly="props.readOnly"
      />
      <VSelect
        id="category_id"
        v-model="form.category_id"
        filter
        :options="props.categories"
        :label="__('requests.form.category_id.label')"
        :placeholder="__('generics.please_select')"
        :message="form.errors.category_id"
        :readonly="props.readOnly"
      />
      <VSelect
        id="urgency_type_id"
        v-model="form.urgency_type_id"
        filter
        :options="props.urgency_types"
        :label="__('requests.form.urgency_type_id.label')"
        :placeholder="__('generics.please_select')"
        :message="form.errors.urgency_type_id"
        :readonly="props.readOnly"
      />
      <VSelect
        id="assigned_professional_id"
        v-model="form.assigned_professional_id"
        filter
        :options="props.professionals"
        :label="__('requests.form.assigned_professional_id.label')"
        :placeholder="__('generics.please_select')"
        :message="form.errors.assigned_professional_id"
        :readonly="props.readOnly"
      />
      <VInput
        id="title"
        v-model="form.title"
        :label="__('requests.form.title.label')"
        :message="form.errors.title"
        :readonly="props.readOnly"
        classWrapper="col-span-2"
      />
      <VInput
        id="description"
        v-model="form.description"
        type="textarea"
        :label="__('requests.form.description.label')"
        :message="form.errors.description"
        :readonly="props.readOnly"
        classWrapper="col-span-2"
      />
      <VSelect
        id="status"
        v-model="form.status"
        :options="statusOptions"
        :label="__('requests.form.status.label')"
        :placeholder="__('generics.please_select')"
        :message="form.errors.status"
        :readonly="props.readOnly"
      />
      <VInput
        id="priority"
        v-model="form.priority"
        type="number"
        :label="__('requests.form.priority.label')"
        :message="form.errors.priority"
        :readonly="props.readOnly"
      />
      <VInput
        id="sla_due_at"
        v-model="form.sla_due_at"
        type="date"
        :label="__('requests.form.sla_due_at.label')"
        :message="form.errors.sla_due_at"
        :readonly="props.readOnly"
      />
      <VInput
        id="accepted_at"
        v-model="form.accepted_at"
        type="date"
        :label="__('requests.form.accepted_at.label')"
        :message="form.errors.accepted_at"
        :readonly="props.readOnly"
      />
    </CardSection>
  </form>
</template>
