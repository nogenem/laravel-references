<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    modelValue: {
        type: String,
        default: '',
    },
});

defineEmits(['update:modelValue']);

const textarea = ref(null);

onMounted(() => {
    if (textarea.value.hasAttribute('autofocus')) {
        textarea.value.scrollIntoView({ behavior: 'smooth' });
        textarea.value.focus({ preventScroll: true });
    }
});
</script>

<template>
    <textarea
        ref="textarea"
        rows="4"
        class="block w-full rounded-md border-gray-300 shadow-sm scrollbar-thin scrollbar-track-gray-200 scrollbar-thumb-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
    />
</template>
