<script setup>
import { useAttrs } from 'vue';

import Password from 'primevue/password';

const model = defineModel();
const attrs = useAttrs();

const props = defineProps({});

const emit = defineEmits(['change', 'input', 'focus', 'blur', 'keydown']);
</script>

<template>
  <Password
    class="h-10 mt-1"
    v-bind="attrs"
    v-model="model"
    @update:modelValue="emit('change', $event)"
    @input="emit('input', $event)"
    @focus="emit('focus', $event)"
    @blur="emit('blur', $event)"
    @keydown="emit('keydown', $event)"
  />
</template>
