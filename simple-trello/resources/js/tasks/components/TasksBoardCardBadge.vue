<script setup>
import { computed } from 'vue';
import { useTasksStore } from '../stores/useTasksStore';

const props = defineProps({
    priority: {
        type: String,
        required: true,
    },
});

const tasksStore = useTasksStore();

const colorsVariants = {
    [tasksStore.priorities.LOW]: {
        wrapper: 'bg-slate-100 text-slate-700',
        dot: 'bg-slate-400',
    },
    [tasksStore.priorities.MEDIUM]: {
        wrapper: 'bg-teal-100 text-teal-700',
        dot: 'bg-teal-400',
    },
    [tasksStore.priorities.HIGH]: {
        wrapper: 'bg-red-100 text-red-700',
        dot: 'bg-red-400',
    },
    default: {
        wrapper: 'bg-zinc-100 text-zinc-700',
        dot: 'bg-zinc-400',
    },
};

const colorVariant = computed(
    () => colorsVariants[props.priority] || colorsVariants['default']
);
</script>

<template>
    <div
        class="flex h-6 items-center rounded-full px-3 text-xs font-semibold"
        :class="`${colorVariant.wrapper}`"
        :title="`${priority} priority`"
    >
        <span
            class="mr-1 h-2 w-2 rounded-full"
            :class="`${colorVariant.dot}`"
        ></span>
        <span>
            {{ priority }}
        </span>
    </div>
</template>
