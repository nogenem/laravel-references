<script setup>
import { onMounted, onUnmounted } from 'vue';
import Button from './Button.vue';

const props = defineProps({
    show: Boolean,
    size: {
        type: String,
        default: '2xl',
    },
});

const emit = defineEmits(['close']);

const modalSizeClasses = {
    xs: 'max-w-xs',
    sm: 'max-w-sm',
    md: 'max-w-md',
    lg: 'max-w-lg',
    xl: 'max-w-xl',
    '2xl': 'max-w-2xl',
    '3xl': 'max-w-3xl',
    '4xl': 'max-w-4xl',
    '5xl': 'max-w-5xl',
    '6xl': 'max-w-6xl',
    '7xl': 'max-w-7xl',
};

const closeOnEscape = (e) => {
    if (props.show && e.key === 'Escape') {
        emit('close');
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));
</script>

<template>
    <transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0"
        leave-active-class="transition ease-in duration-75"
        leave-to-class="opacity-0"
    >
        <div v-if="show">
            <div class="fixed inset-0 z-40 bg-gray-900 bg-opacity-50" />
            <div
                tabindex="-1"
                class="fixed left-0 right-0 top-0 z-50 flex h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden md:inset-0 md:h-full"
                @click.prevent="$emit('close')"
            >
                <div
                    class="relative h-full w-full p-2 md:h-auto"
                    :class="`${modalSizeClasses[size]}`"
                    @click.stop=""
                >
                    <!-- Modal content -->
                    <div
                        class="modal-content relative h-auto overflow-hidden rounded-lg bg-white shadow"
                        dusk="modal"
                    >
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between rounded-t p-4"
                            :class="
                                $slots.header ? 'border-b border-gray-200 ' : ''
                            "
                        >
                            <slot name="header" />
                            <Button
                                type="button"
                                outline
                                icon
                                @click="$emit('close')"
                            >
                                <slot name="close-icon">
                                    <font-awesome-icon
                                        class="h-4 w-4"
                                        icon="fa-solid fa-xmark"
                                    />
                                </slot>
                            </Button>
                        </div>
                        <!-- Modal body -->
                        <div
                            class="h-full overflow-auto p-6 scrollbar-thin scrollbar-track-gray-200 scrollbar-thumb-gray-300"
                            :class="`${$slots.header ? '' : 'pt-0'} ${
                                $slots.footer ? 'max-h-[84vh]' : 'max-h-[90vh]'
                            }`"
                        >
                            <slot name="body" />
                        </div>
                        <!-- Modal footer -->
                        <div
                            v-if="$slots.footer"
                            class="rounded-b border-t border-gray-200 p-2"
                        >
                            <slot name="footer" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<style scoped>
.modal-content {
    /* 100dvh - some spacing */
    max-height: calc(100dvh - 2rem);
}
</style>
