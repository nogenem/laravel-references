<script setup>
import { ref } from 'vue';

import Input from '~/shared/components/Input.vue';

const emit = defineEmits(['save', 'cancel']);

const title = ref('');

function handleSubmit() {
    if (title.value.trim() !== '') {
        emit('save', title.value.trim());
    } else {
        emit('cancel');
    }
}

function handleBlur() {
    if (title.value.trim() === '') {
        emit('cancel');
    }
}
</script>

<template>
    <li
        class="not-sortable rounded border border-white bg-white px-3 py-3 shadow"
        dusk="editable-tasks-board-card"
    >
        <form @submit.prevent="handleSubmit">
            <Input
                v-model="title"
                type="text"
                name="title"
                placeholder="Enter with a title for this card"
                maxlength="100"
                autofocus
                class="block w-full"
                @blur="handleBlur"
                @keyup.esc="$emit('cancel')"
            />
        </form>
    </li>
</template>
