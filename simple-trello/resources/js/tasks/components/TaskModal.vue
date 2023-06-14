<script setup>
/* eslint-disable vue/no-v-html */

import { format, parseISO } from 'date-fns';
import * as DOMPurify from 'dompurify';
import cloneDeep from 'lodash.clonedeep';
import { marked } from 'marked';
import { storeToRefs } from 'pinia';
import { computed, ref, toRaw } from 'vue';

import Button from '~/shared/components/Button.vue';
import ErrorsDisplay from '~/shared/components/ErrorsDisplay.vue';
import Input from '~/shared/components/Input.vue';
import Modal from '~/shared/components/Modal.vue';
import SearchableSelect from '~/shared/components/SearchableSelect.vue';
import Select from '~/shared/components/Select.vue';
import Textarea from '~/shared/components/Textarea.vue';
import { useSignedInUserStore } from '~/shared/stores/useSignedInUserStore';
import formatDate from '~/shared/utils/formatDate';
import useSearchUsers from '../composables/useSearchUsers';
import { useTasksStore } from '../stores/useTasksStore';
import { canUserDeleteTask, canUserEditTask } from '../taskPolicy';
import TasksBoardCardBadge from './TasksBoardCardBadge.vue';

const props = defineProps({
    task: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(['close']);

const taskBeingEdited = ref(null);

const signedInUserStore = useSignedInUserStore();
const tasksStore = useTasksStore();
const { errorsByStatus } = storeToRefs(tasksStore);

const { users, errors: searchUsersErrors, searchUsers } = useSearchUsers();

const canUserEditThisTask = computed(() =>
    canUserEditTask(signedInUserStore.signedInUser, props.task)
);
const canUserDeleteThisTask = computed(() =>
    canUserDeleteTask(signedInUserStore.signedInUser, props.task)
);
const statusesArray = computed(() => Object.values(tasksStore.statuses));
const prioritiesArray = computed(() => Object.values(tasksStore.priorities));
const errors = computed(() => ({
    ...(errorsByStatus.value[props.task.status] || {}),
    ...searchUsersErrors.value,
}));
const mdParsedDescription = computed(() =>
    DOMPurify.sanitize(marked.parse(props.task.description || ''))
);

function handleEditTask() {
    const clonedTask = cloneDeep(toRaw(props.task));

    // Parse date to local time and to a format that the datetime-local input
    // can understand
    if (clonedTask.deadline) {
        clonedTask.deadline = format(
            parseISO(clonedTask.deadline),
            "yyyy-MM-dd'T'HH:mm"
        );
    }

    if (!clonedTask.assignedTo) {
        clonedTask.assignedTo = {
            id: '',
            name: '',
        };
    }

    taskBeingEdited.value = clonedTask;
}

function handleCancelEditTask() {
    taskBeingEdited.value = null;
}

function handleCloseModal() {
    taskBeingEdited.value = null;
    emit('close');
}

async function handleSaveEditedTask() {
    const taskToBeUpdated = cloneDeep(taskBeingEdited.value);

    // Parse it back to UTC ISO string
    if (taskToBeUpdated.deadline) {
        taskToBeUpdated.deadline = parseISO(
            taskToBeUpdated.deadline
        ).toISOString();
    }

    if (taskToBeUpdated.assignedTo && taskToBeUpdated.assignedTo.id) {
        taskToBeUpdated.assigned_to = taskToBeUpdated.assignedTo.id;
    }

    const updatedTask = await tasksStore.updateTask(
        taskToBeUpdated,
        props.task.status
    );

    if (updatedTask) {
        taskBeingEdited.value = null;
    }
}

async function handleDeleteTask() {
    if (window.confirm('Are you sure you want to delete this task?')) {
        await tasksStore.deleteTask(props.task);
    }
}

async function handleAssignedToSelectSearch(searchValue) {
    searchUsers(searchValue);
}
</script>

<template>
    <Modal :show="task !== null" size="4xl" @close="handleCloseModal">
        <template #header>
            <div class="flex items-center text-lg">Task information</div>
        </template>
        <template #body>
            <ErrorsDisplay :errors="errors" class="mb-4" />
            <div class="flex flex-col justify-between gap-4 md:flex-row">
                <div class="flex w-full flex-grow flex-col gap-4 md:w-3/4">
                    <!-- Title -->
                    <h1
                        v-if="!taskBeingEdited"
                        class="font-sans text-lg font-semibold tracking-wide text-gray-700"
                    >
                        {{ task.title }}
                    </h1>
                    <Input
                        v-if="taskBeingEdited"
                        v-model="taskBeingEdited.title"
                        name="title"
                        type="text"
                        maxlength="100"
                        autofocus
                        required
                        class="block w-full"
                    />

                    <div
                        v-if="task.deadline || task.priority || taskBeingEdited"
                        class="flex items-center gap-4"
                        :class="{
                            'flex-col md:flex-row': !!taskBeingEdited,
                        }"
                    >
                        <!-- Status -->
                        <span
                            v-if="!taskBeingEdited"
                            :title="`Status: ${task.status}`"
                            class="font-sans text-sm font-semibold tracking-normal text-gray-700"
                        >
                            {{ task.status }}
                        </span>
                        <Select
                            v-if="taskBeingEdited"
                            v-model="taskBeingEdited.status"
                            name="status"
                        >
                            <option
                                v-for="status in statusesArray"
                                :key="status"
                            >
                                {{ status }}
                            </option>
                        </Select>

                        <!-- Deadline -->
                        <span
                            v-if="task.deadline && !taskBeingEdited"
                            :title="`Deadline: ${formatDate(task.deadline)}`"
                            class="text-sm text-gray-600"
                        >
                            {{ formatDate(task.deadline) }}
                        </span>
                        <Input
                            v-if="taskBeingEdited"
                            v-model="taskBeingEdited.deadline"
                            name="deadline"
                            type="datetime-local"
                            class="block"
                        />

                        <!-- Priority -->
                        <TasksBoardCardBadge
                            v-if="task.priority && !taskBeingEdited"
                            :title="`Priority: ${task.priority}`"
                            :priority="task.priority"
                        />
                        <Select
                            v-if="taskBeingEdited"
                            v-model="taskBeingEdited.priority"
                            name="priority"
                        >
                            <option
                                v-for="priority in prioritiesArray"
                                :key="priority"
                            >
                                {{ priority }}
                            </option>
                        </Select>
                    </div>

                    <!-- Description -->
                    <p
                        v-if="!taskBeingEdited"
                        class="prose"
                        v-html="mdParsedDescription"
                    />
                    <Textarea
                        v-if="taskBeingEdited"
                        v-model="taskBeingEdited.description"
                        name="description"
                        rows="12"
                    />
                </div>
                <div
                    class="mt-4 flex w-full flex-grow flex-col items-start justify-start gap-4 md:mt-0 md:w-1/4"
                >
                    <!-- Created by -->
                    <div>
                        <p
                            class="font-sans text-sm font-semibold tracking-wide text-gray-700"
                        >
                            Created by:
                        </p>
                        <p
                            class="text-md truncate font-sans font-semibold tracking-wide text-gray-700"
                        >
                            {{ task.createdBy.name }}
                        </p>
                    </div>

                    <!-- Assigned to -->
                    <div>
                        <p
                            class="font-sans text-sm font-semibold tracking-wide text-gray-700"
                        >
                            Assigned to:
                        </p>
                        <p
                            v-if="!taskBeingEdited && task.assignedTo"
                            class="text-md truncate font-sans font-semibold tracking-wide text-gray-700"
                        >
                            {{ task.assignedTo.name }}
                        </p>

                        <SearchableSelect
                            v-if="taskBeingEdited"
                            v-model="taskBeingEdited.assignedTo"
                            id-key="id"
                            text-key="name"
                            name="assigned_to"
                            :options="users"
                            @search="handleAssignedToSelectSearch"
                        />
                    </div>
                </div>
            </div>
        </template>
        <template v-if="canUserEditThisTask" #footer>
            <div class="flex justify-end gap-4">
                <Button
                    v-if="!taskBeingEdited && canUserDeleteThisTask"
                    variant="error"
                    @click="handleDeleteTask"
                >
                    Delete
                </Button>
                <Button v-if="!taskBeingEdited" @click="handleEditTask">
                    Edit
                </Button>

                <Button
                    v-if="taskBeingEdited"
                    outline
                    @click="handleCancelEditTask"
                >
                    Cancel
                </Button>
                <Button v-if="taskBeingEdited" @click="handleSaveEditedTask">
                    Save
                </Button>
            </div>
        </template>
    </Modal>
</template>
