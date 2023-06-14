require('./bootstrap');

import Alpine from 'alpinejs';
import { createPinia } from 'pinia';
import { createApp } from 'vue';

import { library } from '@fortawesome/fontawesome-svg-core';
import {
    faCheck,
    faEllipsisVertical,
    faPlus,
    faSort,
    faSortDown,
    faSortUp,
    faXmark
} from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

import TasksBoard from '~/tasks/components/TasksBoard.vue';

window.Alpine = Alpine;
Alpine.start();

const pinia = createPinia();

library.add(faXmark, faPlus, faEllipsisVertical, faSort, faSortUp, faSortDown, faCheck);

createApp({
    components: {
        TasksBoard,
    },
})
    .component('font-awesome-icon', FontAwesomeIcon)
    .use(pinia)
    .mount('#app');
