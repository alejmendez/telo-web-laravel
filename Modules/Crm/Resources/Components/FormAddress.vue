<script setup>
import CardSection from '@Core/Components/CardSection.vue';
import VInput from '@Core/Components/Form/VInput.vue';
import VInputLocation from '@Core/Components/Form/VInputLocation.vue';
import VCheckbox from '@Core/Components/Form/VCheckbox.vue';
import Button from '@Core/Components/Form/Button.vue';

import LocationService from '@Crm/Services/LocationService.js';

const props = defineProps({
  form: Object,
  locations: Array,
  readOnly: Boolean,
});

const locations_nodes = LocationService.createNodes(props.locations);

const add_address = () => {
  props.form.addresses.push({
    id: null,
    location_id: null,
    address: null,
    postal_code: null,
    is_primary: false,
  });
};

const remove_address = (index) => {
  props.form.addresses.splice(index, 1);
};

const check_primary = (index) => {
  props.form.addresses.forEach((address, i) => {
    if (i !== index) {
      address.is_primary = false;
    }
  });
};

const get_location_name = (location_id) => {
  location_id = parseInt(Object.keys(location_id)[0]);
  const location = props.locations.find((location) => location.id === location_id);
  return location ? location.name : '';
};
</script>

<template>
  <CardSection
    :headerText="__('address.title')"
    wrapperClass="pt-4"
  >
    <div
      class="px-6 py-2 grid grid-cols-2 gap-x-16 gap-y-4 relative"
      :class="{'pb-6': props.readOnly, 'border-b-1 border-gray-500': !props.readOnly}"
      v-for="(address, index) in form.addresses"
      :key="index"
    >
      <VInput
        :id="`location_id_${index}`"
        :value="get_location_name(address.location_id)"
        :label="__('address.form.location_id.label')"
        :readonly="props.readOnly"
        v-if="props.readOnly"
      />

      <VInputLocation
        :id="`location_id_${index}`"
        v-model="address.location_id"
        filter
        filterMode="lenient"
        :options="locations_nodes"
        :label="__('address.form.location_id.label')"
        :placeholder="__('generics.please_select')"
        :message="form.errors[`addresses.${index}.location_id`]"
        v-if="!props.readOnly"
      />

      <div class="grid grid-cols-12 gap-x-16 gap-y-4">
        <VInput
          :id="`postal_code_${index}`"
          v-model="address.postal_code"
          :type="props.readOnly ? 'text' : 'number'"
          classWrapper="col-span-10"
          :useGrouping="false"
          :max="999999999"
          :label="__('address.form.postal_code.label')"
          :message="form.errors[`addresses.${index}.postal_code`]"
          :readonly="props.readOnly"
        />
      </div>

      <VInput
        :id="`address_${index}`"
        v-model="address.address"
        type="textarea"
        maxlength="200"
        :label="__('address.form.address.label')"
        :message="form.errors[`addresses.${index}.address`]"
        :readonly="props.readOnly"
      />

      <VCheckbox
        v-model="address.is_primary"
        classWrapper="pt-6"
        @click="check_primary(index)"
        :label="__('address.form.is_primary.label')"
        :message="form.errors[`addresses.${index}.is_primary`]"
        :disabled="props.readOnly"
      />

      <div
        class="pt-8 text-black hover:text-red-500 dark:text-gray-400 cursor-pointer absolute top-2 right-5"
        v-if="index !== 0 && !props.readOnly"
        @click="remove_address(index)"
      >
        <span class="material-symbols-rounded">delete</span>
      </div>
    </div>
    <div class="px-6 pt-4 pb-6" v-if="!props.readOnly">
      <Button
        class="btn btn-secondary border-gray-800"
        @click.prevent="add_address"
        :label="__('address.buttons.add_address')"
      />
    </div>
  </CardSection>
</template>
