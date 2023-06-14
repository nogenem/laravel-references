<script setup>
// PS: Copied from Laravel Breeze, to keep the styling of the Auth pages
import { computed } from 'vue';

const props = defineProps({
    type: {
        type: String,
        default: 'submit',
    },
    variant: {
        type: String,
        default: 'primary',
    },
    outline: Boolean,
    disabled: Boolean,
    icon: Boolean,
    transparent: Boolean,
});

const buttonColorClasses = {
    default: {
        primary: 'bg-gray-800 text-white border-transparent',
        error: 'bg-red-800 text-white border-transparent',
    },
    hover: {
        primary: 'hover:bg-gray-700',
        error: 'hover:bg-red-700',
    },
};

const buttonOutlineColorClasses = {
    default: {
        primary: 'bg-transparent text-gray-800 border-gray-800',
        error: 'bg-transparent text-white border-red-800',
    },
    hover: {
        primary: 'hover:bg-gray-800 hover:text-white hover:border-transparent',
        error: 'hover:bg-red-800 hover:text-white hover:border-transparent',
    },
};

const buttonTransparentColorClasses = {
    default: {
        primary: 'bg-transparent text-gray-800 border-transparent',
        error: 'bg-transparent text-red-800 border-transparent',
    },
    hover: {
        primary: 'hover:bg-gray-200 hover:border-gray-200',
        error: 'hover:bg-red-200 hover:border-red-200',
    },
};

const buttonDisabledClasses = 'disabled:opacity-50 disabled:cursor-not-allowed';

const buttonPaddingClasses = {
    default: 'px-4 py-2',
    icon: 'p-1.5',
};

const buttonClasses = computed(() => {
    let colorClasses =
        buttonColorClasses.default[props.variant] ||
        buttonColorClasses.default['primary'];
    let hoverClasses =
        buttonColorClasses.hover[props.variant] ||
        buttonColorClasses.hover['primary'];
    let paddingClasses = buttonPaddingClasses.default;

    if (props.outline) {
        colorClasses =
            buttonOutlineColorClasses.default[props.variant] ||
            buttonOutlineColorClasses.default['primary'];
        hoverClasses =
            buttonOutlineColorClasses.hover[props.variant] ||
            buttonOutlineColorClasses.hover['primary'];
    } else if (props.transparent) {
        colorClasses =
            buttonTransparentColorClasses.default[props.variant] ||
            buttonTransparentColorClasses.default['primary'];
        hoverClasses =
            buttonTransparentColorClasses.hover[props.variant] ||
            buttonTransparentColorClasses.hover['primary'];
    }

    if (props.disabled) {
        hoverClasses = buttonDisabledClasses;
    }

    if (props.icon) {
        paddingClasses = buttonPaddingClasses.icon;
    }

    return `${colorClasses} ${hoverClasses} ${paddingClasses}`;
});
</script>

<template>
    <button
        :type="type"
        class="focus:shadow-outline-gray inline-flex items-center rounded-md border text-xs font-semibold uppercase tracking-widest transition duration-150 ease-in-out focus:outline-none"
        :class="buttonClasses"
        :disabled="disabled"
    >
        <slot />
    </button>
</template>
