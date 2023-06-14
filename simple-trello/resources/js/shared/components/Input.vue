<script setup>
// PS: Copied from Laravel Breeze, to keep the styling of the Auth pages
import { onMounted, ref } from 'vue';

defineProps({
    modelValue: {
        type: String,
        default: '',
    },
});

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.scrollIntoView({ behavior: 'smooth' });
        input.value.focus({ preventScroll: true });
    }
});
</script>

<template>
    <input
        ref="input"
        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
    />
</template>
