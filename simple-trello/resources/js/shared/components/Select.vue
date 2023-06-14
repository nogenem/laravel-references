<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    modelValue: {
        type: String,
        default: '',
    },
});

defineEmits(['update:modelValue']);

const select = ref(null);

onMounted(() => {
    if (select.value.hasAttribute('autofocus')) {
        select.value.scrollIntoView({ behavior: 'smooth' });
        select.value.focus({ preventScroll: true });
    }
});
</script>

<template>
    <select
        ref="select"
        class="rounded-md border-gray-300 shadow-sm scrollbar-thin scrollbar-track-gray-200 scrollbar-thumb-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
        :value="modelValue"
        @change="$emit('update:modelValue', $event.target.value)"
    >
        <slot />
    </select>
</template>
