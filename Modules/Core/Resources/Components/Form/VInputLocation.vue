<script setup>
import { useAttrs, ref, watch } from 'vue';

import TreeSelect from 'primevue/treeselect';

import VElementFormWrapper from '@Core/Components/Form/VElementFormWrapper.vue';

const model = defineModel();

const attrs = useAttrs();
const expandedKeys = ref({});

const props = defineProps({
  classWrapper: {
    type: String,
    default: '',
  },
  label: {
    type: String,
    default: '',
  },
  message: {
    type: String,
  },
  options: {
    type: Array,
    default: [],
  },
});

const emit = defineEmits(['change', 'input', 'click', 'focus', 'blur', 'keydown']);

const isInvalid = props.message !== '' && props.message !== undefined;

watch(() => props.options, (newOptions) => {
  if (newOptions && newOptions.length > 0) {
    expandedKeys.value = { [newOptions[0].key]: true };
  }
}, { immediate: true });
</script>

<template>
  <VElementFormWrapper :classWrapper="props.classWrapper" :label="props.label" :message="props.message">
    <TreeSelect
      v-bind="attrs"
      v-model="model"
      v-model:expandedKeys="expandedKeys"
      fluid
      :options="props.options"
      :invalid="isInvalid"
      @update:modelValue="emit('change', $event)"
      @input="emit('input', $event)"
      @click="emit('click', $event)"
      @focus="emit('focus', $event)"
      @blur="emit('blur', $event)"
      @keydown="emit('keydown', $event)"
    />
  </VElementFormWrapper>
</template>
