<script setup>
import { onMounted } from 'vue';

import { useSignedInUserStore } from '~/shared/stores/useSignedInUserStore';
import { useTasksStore } from '../stores/useTasksStore';
import TasksBoardTrack from './TasksBoardTrack.vue';

const props = defineProps({
    signedInUser: {
        type: Object,
        required: true,
    },
    statuses: {
        type: Object,
        required: true,
    },
    priorities: {
        type: Object,
        required: true,
    },
});

const signedInUserStore = useSignedInUserStore();
const tasksStore = useTasksStore();

onMounted(() => {
    signedInUserStore.setSignedInUser(props.signedInUser);
    tasksStore.setStatuses(props.statuses);
    tasksStore.setPriorities(props.priorities);
});
</script>

<template>
    <div
        class="tasks-board flex w-full justify-center overflow-y-auto scrollbar-thin scrollbar-track-gray-200 scrollbar-thumb-gray-300"
    >
        <ul class="flex gap-6 bg-white p-4 lg:gap-4">
            <TasksBoardTrack :status="statuses.PENDING" />
            <TasksBoardTrack :status="statuses.IN_PROGRESS" />
            <TasksBoardTrack :status="statuses.CONCLUDED" />
        </ul>
    </div>
</template>
