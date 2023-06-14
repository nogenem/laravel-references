<script setup>
import { storeToRefs } from 'pinia';
import Sortable from 'sortablejs';
import { computed, nextTick, onMounted, ref } from 'vue';

import Button from '~/shared/components/Button.vue';
import ErrorsDisplay from '~/shared/components/ErrorsDisplay.vue';
import { useSignedInUserStore } from '~/shared/stores/useSignedInUserStore';
import { useTasksStore } from '../stores/useTasksStore';
import { canUserEditTask } from '../taskPolicy';
import EditableTasksBoardCard from './EditableTasksBoardCard.vue';
import TaskModal from './TaskModal.vue';
import TasksBoardCard from './TasksBoardCard.vue';
import TasksBoardTrackOptionsMenu from './TasksBoardTrackOptionsMenu.vue';

const props = defineProps({
    status: {
        type: String,
        required: true,
    },
});

const isCreatingTask = ref(false);
const cardsUl = ref(null);
const selectedTaskId = ref(null);
const lastFuncExecutedBeforeServerError = ref(null);

const signedInUserStore = useSignedInUserStore();
const { signedInUser } = storeToRefs(signedInUserStore);

const tasksStore = useTasksStore();
const { tasksByStatus, errorsByStatus } = storeToRefs(tasksStore);
const {
    fetchTasksByStatus,
    clearErrorsByStatus,
    saveTask,
    changeSortOptionByStatus,
    moveAndUpdateTask,
} = tasksStore;

const tasks = computed(() => tasksByStatus.value[props.status] || []);
const selectedTask = computed(() =>
    tasks.value.find((t) => t.id === selectedTaskId.value)
);
const errors = computed(() => errorsByStatus.value[props.status] || {});

async function handleShowEditableCard() {
    isCreatingTask.value = true;
}

function handleHideEditableCard() {
    clearErrorsByStatus(props.status);
    isCreatingTask.value = false;
}

async function handleSaveTask(title) {
    const newTask = await saveTask({ title, status: props.status });

    if (newTask) {
        handleHideEditableCard();
    } else {
        await nextTick();

        // Keep it at the bottom
        //   Showing the ErrorsDisplay for the first time makes
        //   the ul container scroll up a bit
        cardsUl.value.scrollTop = cardsUl.value.scrollHeight;
    }
}

function handleTaskCardClick(taskId) {
    selectedTaskId.value = taskId;
    window.history.pushState(null, '', `?selectedTaskId=${taskId}`);
}

function closeModal() {
    selectedTaskId.value = null;
    window.history.pushState(null, '', '?');
}

function retry() {
    if (lastFuncExecutedBeforeServerError.value) {
        lastFuncExecutedBeforeServerError.value();
    }
}

async function handleChangeSortOption(option) {
    await changeSortOptionByStatus(props.status, option);
}

function undoDragEvent(item, fromList, fromIdx) {
    fromList.insertBefore(
        item,
        fromList.querySelector(`li:nth-child(${fromIdx + 1})`)
    );
}

function strToIntOrNull(str) {
    if (str === null || str === undefined) {
        return null;
    } else if (typeof str === 'number') {
        return str;
    } else if (typeof str !== 'string') {
        return null;
    } else if (!/^[0-9]+$/.test(str.trim())) {
        return null;
    }
    return parseInt(str.trim());
}

onMounted(async () => {
    lastFuncExecutedBeforeServerError.value = fetchTasksByStatus.bind(
        tasksStore,
        props.status
    );
    await lastFuncExecutedBeforeServerError.value();

    const queryParams = new URLSearchParams(window.location.search);
    const selectedTaskIdFromQueryParam = strToIntOrNull(
        queryParams.get('selectedTaskId')
    );
    if (selectedTaskIdFromQueryParam === null) {
        window.history.pushState(null, '', '?');
    } else {
        selectedTaskId.value = selectedTaskIdFromQueryParam;
    }

    Sortable.create(cardsUl.value, {
        group: 'tasks-board-cards',
        filter: '.not-sortable',
        onStart: function (event) {
            this._oldIndex = event.oldIndex;
        },
        onMove: function (event) {
            const task = tasks.value[this._oldIndex];

            // User is trying to change the Status of the task
            if (
                !task ||
                (event.from !== event.to &&
                    !canUserEditTask(signedInUser.value, task))
            ) {
                this._canceled = true;
                return false;
            } else {
                this._canceled = false;
                return true;
            }
        },
        onEnd: async function (event) {
            if (this._canceled) {
                undoDragEvent(event.item, event.from, event.oldIndex);
                return;
            }

            lastFuncExecutedBeforeServerError.value = moveAndUpdateTask.bind(
                tasksStore,
                event.from.dataset.status,
                event.oldIndex,
                event.to.dataset.status,
                event.newIndex
            );

            const updatedTask = await lastFuncExecutedBeforeServerError.value();

            if (!updatedTask) {
                undoDragEvent(event.item, event.from, event.oldIndex);
            } else {
                clearErrorsByStatus(props.status);
            }
        },
    });
});
</script>

<template>
    <li
        :dusk="`tasks-board-track-${status}`"
        class="tasks-board-track column-width flex w-80 min-w-[20rem] flex-col rounded-lg bg-gray-50 px-3 py-3"
    >
        <div class="flex items-center justify-between">
            <div
                class="pb-2 font-sans text-sm font-semibold tracking-wide text-gray-700"
            >
                {{ status }}
            </div>

            <TasksBoardTrackOptionsMenu
                :status="status"
                @change-sort-option="handleChangeSortOption"
            />
        </div>
        <ErrorsDisplay
            :errors="errors"
            :allow-retry="
                !isCreatingTask &&
                !selectedTaskId &&
                !!lastFuncExecutedBeforeServerError
            "
            class="p-1"
            @retry="retry"
        />
        <ul
            ref="cardsUl"
            class="max-h-full flex-grow overflow-auto p-1 scrollbar-thin scrollbar-track-gray-100 scrollbar-thumb-gray-200"
            :data-status="status"
        >
            <TasksBoardCard
                v-for="task in tasks"
                :key="task.id"
                :task="task"
                class="mt-4 cursor-pointer"
                @click="handleTaskCardClick"
            />
            <EditableTasksBoardCard
                v-if="isCreatingTask"
                class="mt-4 cursor-pointer"
                @save="handleSaveTask"
                @cancel="handleHideEditableCard"
            />
        </ul>
        <Button
            type="button"
            outline
            class="mt-1 inline-flex items-center gap-4"
            :dusk="`tasks-board-add-card-${status}`"
            @click="handleShowEditableCard"
        >
            <font-awesome-icon class="h-4 w-4" icon="fa-solid fa-plus" />
            Add a card
        </Button>
    </li>
    <Teleport to="body">
        <TaskModal :task="selectedTask" @close="closeModal" />
    </Teleport>
</template>

<style>
.tasks-board-track {
    /* 100dvh - nav heigh - ul padding */
    height: calc(100dvh - 65px - 2rem);
}

.tasks-board-track > ul > li:first-child {
    margin-top: 0;
}
</style>
