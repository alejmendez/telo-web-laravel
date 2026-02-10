<script setup>
import CardSection from '@Core/Components/CardSection.vue';
import VInput from '@Core/Components/Form/VInput.vue';
import VSelect from '@Core/Components/Form/VSelect.vue';
import Button from '@Core/Components/Form/Button.vue';

const props = defineProps({
  form: Object,
  contact_types: Array,
  readOnly: Boolean,
});

const add_contact = () => {
  props.form.contacts.push({
    id: null,
    contact_type: null,
    content: null,
  });
};

const remove_contact = (index) => {
  props.form.contacts.splice(index, 1);
};
</script>

<template>
  <CardSection
    :headerText="__('contact.title')"
    wrapperClass="pt-4"
  >
    <div
      class="px-6 py-2 grid grid-cols-2 gap-x-16 gap-y-4"
      v-for="(contact, index) in form.contacts"
      :key="index"
    >
      <VSelect
        v-model="contact.contact_type"
        :options="props.contact_types"
        :label="__('contact.form.contact_type.label')"
        :placeholder="__('generics.please_select')"
        :message="form.errors[`contacts.${index}.contact_type`]"
        :readonly="props.readOnly"
      />
      <div class="grid grid-cols-9 gap-x-2 gap-y-4">
        <div class="col-span-8">
          <VInput
            v-model="contact.content"
            :label="__('contact.form.content.label')"
            :message="form.errors[`contacts.${index}.content`]"
            :readonly="props.readOnly"
          />
        </div>
        <div
          class="pt-8 text-black hover:text-red-500 dark:text-gray-400 cursor-pointer"
          v-if="index !== 0 && !props.readOnly"
          @click="remove_contact(index)"
        >
          <span class="material-symbols-rounded">delete</span>
        </div>
      </div>

    </div>
    <div class="px-6 pt-4 pb-6">
      <Button
        class="btn btn-secondary border-gray-800"
        @click.prevent="add_contact"
        :label="__('customer.buttons.add_contact')"
      />
    </div>
  </CardSection>
</template>
