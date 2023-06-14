import axios from 'axios';
import cloneDeep from 'lodash.clonedeep';
import { defineStore } from 'pinia';

import getServerErrors from '~/shared/utils/getServerErrors';

export const useTasksStore = defineStore('tasks', {
    state: () => ({
        tasksByStatus: {},
        sortOptionByStatus: {},
        errorsByStatus: {},
        statuses: {},
        priorities: {},
    }),
    actions: {
        setStatuses(statuses) {
            this.statuses = statuses;
        },
        setPriorities(priorities) {
            this.priorities = priorities;
        },
        clearErrorsByStatus(status) {
            this.errorsByStatus[status] = {};
        },
        async fetchTasksByStatus(status) {
            try {
                if (!this.sortOptionByStatus[status]) {
                    this.sortOptionByStatus[status] = '-deadline';
                }
                const sort = this.sortOptionByStatus[status];

                const response = await axios.get(
                    `/api/tasks?status=${status}&sort=${sort}`
                );

                this.clearErrorsByStatus(status);
                this.tasksByStatus[status] = response.data.data;
            } catch (err) {
                this.errorsByStatus[status] = getServerErrors(err);
                console.error('Error while fetching tasks by status', err);
            }
        },
        async saveTask(task) {
            try {
                const response = await axios.post('/api/tasks', task);
                const newTask = response.data;

                this.clearErrorsByStatus(newTask.status);
                if (!this.tasksByStatus[newTask.status]) {
                    this.tasksByStatus[newTask.status] = [];
                }
                this.tasksByStatus[newTask.status].push(newTask);

                return newTask;
            } catch (err) {
                this.errorsByStatus[task.status] = getServerErrors(err);
                console.error('Error while saving task', err);
                return null;
            }
        },
        async updateTask(task, oldStatus) {
            try {
                const response = await axios.patch(
                    `/api/tasks/${task.id}`,
                    task
                );
                const updatedTask = response.data;

                this.clearErrorsByStatus(oldStatus);
                if (updatedTask.status !== oldStatus) {
                    this.tasksByStatus[oldStatus] = this.tasksByStatus[
                        oldStatus
                    ].filter((t) => t.id !== updatedTask.id);
                    this.tasksByStatus[updatedTask.status].unshift(updatedTask);
                } else {
                    const taskIdx = this.tasksByStatus[
                        updatedTask.status
                    ].findIndex((t) => t.id === updatedTask.id);
                    this.tasksByStatus[updatedTask.status][taskIdx] =
                        updatedTask;
                }

                return updatedTask;
            } catch (err) {
                this.errorsByStatus[oldStatus] = getServerErrors(err);
                console.error('Error while updating task', err);
                return null;
            }
        },
        async deleteTask(task) {
            try {
                await axios.delete(`/api/tasks/${task.id}`);

                this.clearErrorsByStatus(task.status);

                this.tasksByStatus[task.status] = this.tasksByStatus[
                    task.status
                ].filter((t) => t.id !== task.id);

                return true;
            } catch (err) {
                this.errorsByStatus[task.status] = getServerErrors(err);
                console.error('Error while deleting task', err);
                return false;
            }
        },
        async changeSortOptionByStatus(status, option) {
            let currentOption = this.sortOptionByStatus[status];
            let currentOptionIsDesc = currentOption.startsWith('-');

            if (currentOptionIsDesc) {
                currentOption = currentOption.substring(1);
            }

            if (currentOption === option && !currentOptionIsDesc) {
                this.sortOptionByStatus[status] = `-${option}`;
            } else {
                this.sortOptionByStatus[status] = option;
            }

            return await this.fetchTasksByStatus(status);
        },
        async moveAndUpdateTask(fromStatus, fromIdx, toStatus, toIdx) {
            try {
                let task = cloneDeep(this.tasksByStatus[fromStatus][fromIdx]);

                if (fromStatus !== toStatus) {
                    task.status = toStatus;

                    const response = await axios.patch(
                        `/api/tasks/${task.id}`,
                        task
                    );
                    task = response.data;

                    this.clearErrorsByStatus(fromStatus);
                }

                this.tasksByStatus[fromStatus] = this.tasksByStatus[
                    fromStatus
                ].filter((t) => t.id !== task.id);
                this.tasksByStatus[toStatus].splice(toIdx, 0, task);

                return task;
            } catch (err) {
                this.errorsByStatus[fromStatus] = getServerErrors(err);
                console.error('Error while moving and updating task', err);
                return null;
            }
        },
    },
});
