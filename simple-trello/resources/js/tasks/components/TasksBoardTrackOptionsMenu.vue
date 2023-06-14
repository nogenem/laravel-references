<script setup>
import { storeToRefs } from 'pinia';

import Button from '~/shared/components/Button.vue';
import Dropdown from '~/shared/components/dropdown/Dropdown.vue';
import DropdownButton from '~/shared/components/dropdown/DropdownButton.vue';
import DropdownText from '~/shared/components/dropdown/DropdownText.vue';
import { useTasksStore } from '../stores/useTasksStore';

defineProps({
    status: {
        type: String,
        required: true,
    },
});

defineEmits(['changeSortOption']);

const tasksStore = useTasksStore();
const { sortOptionByStatus } = storeToRefs(tasksStore);
</script>

<template>
    <Dropdown>
        <template #trigger>
            <Button
                type="button"
                dusk="tasks-board-track-options-menu"
                transparent
                icon
            >
                <font-awesome-icon
                    class="h-4 w-4"
                    icon="fa-solid fa-ellipsis-vertical"
                />
            </Button>
        </template>
        <template #content>
            <DropdownText>Sort by...</DropdownText>
            <DropdownButton
                class="flex items-center justify-between"
                @click="$emit('changeSortOption', 'deadline')"
            >
                Deadline
                <font-awesome-icon
                    v-if="sortOptionByStatus[status] === 'deadline'"
                    icon="fa-solid fa-sort-up"
                />
                <font-awesome-icon
                    v-else-if="sortOptionByStatus[status] === '-deadline'"
                    icon="fa-solid fa-sort-down"
                />
                <font-awesome-icon v-else icon="fa-solid fa-sort" />
            </DropdownButton>
            <DropdownButton
                class="flex items-center justify-between"
                @click="$emit('changeSortOption', 'priority')"
            >
                Priority
                <font-awesome-icon
                    v-if="sortOptionByStatus[status] === 'priority'"
                    icon="fa-solid fa-sort-up"
                />
                <font-awesome-icon
                    v-else-if="sortOptionByStatus[status] === '-priority'"
                    icon="fa-solid fa-sort-down"
                />
                <font-awesome-icon v-else icon="fa-solid fa-sort" />
            </DropdownButton>
        </template>
    </Dropdown>
</template>
