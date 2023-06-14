<script setup>
import { computed } from 'vue';

import Button from './Button.vue';

const props = defineProps({
    errors: {
        type: Object,
        required: true,
    },
    allowRetry: Boolean,
});

defineEmits(['retry']);

const hasErrors = computed(() => Object.keys(props.errors).length > 0);
</script>

<template>
    <ul
        v-if="hasErrors"
        class="rounded bg-red-500 px-2 py-2 font-bold text-white shadow-lg"
    >
        <template v-for="(errorsList, errorKey) in errors" :key="errorKey">
            <li v-for="error in errorsList" :key="error" class="text-sm">
                * {{ error }}
            </li>
        </template>
        <Button
            v-if="allowRetry"
            variant="error"
            outline
            class="mt-2"
            @click="$emit('retry')"
        >
            Retry
        </Button>
    </ul>
</template>
