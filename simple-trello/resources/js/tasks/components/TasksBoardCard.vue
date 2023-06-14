<script setup>
import formatDate from '~/shared/utils/formatDate';
import TasksBoardCardBadge from './TasksBoardCardBadge.vue';

defineProps({
    task: {
        type: Object,
        required: true,
    },
});

defineEmits(['click']);
</script>

<template>
    <li
        class="tasks-board-card rounded border border-white bg-white px-3 py-3 shadow"
        :dusk="`tasks-board-card-${task.id}`"
        @click="$emit('click', task.id)"
    >
        <p class="font-sans text-sm font-semibold tracking-wide text-gray-700">
            {{ task.title }}
        </p>
        <div
            v-if="task.deadline || task.priority"
            class="mt-4 flex items-center justify-between"
        >
            <span v-if="task.deadline" class="text-xs text-gray-600">
                {{ formatDate(task.deadline) }}
            </span>
            <TasksBoardCardBadge
                v-if="task.priority"
                :priority="task.priority"
            />
        </div>
    </li>
</template>
