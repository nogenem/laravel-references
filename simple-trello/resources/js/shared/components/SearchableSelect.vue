<script setup>
import debounce from 'lodash.debounce';
import { ref } from 'vue';

import Dropdown from './dropdown/Dropdown.vue';
import DropdownButton from './dropdown/DropdownButton.vue';
import DropdownText from './dropdown/DropdownText.vue';
import Input from './Input.vue';

const props = defineProps({
    modelValue: {
        type: [String, Number, Object],
        default: '',
    },
    idKey: {
        type: String,
        default: '',
    },
    textKey: {
        type: String,
        default: '',
    },
    options: {
        type: Array,
        default() {
            return [];
        },
    },
    placeholder: {
        type: String,
        default: 'Search...',
    },
    name: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['update:modelValue', 'search']);

const search = ref(null);

function getValueFromObject(obj, key) {
    if (obj === null || obj === undefined) {
        return null;
    } else if (typeof obj !== 'object' || !(key in obj)) {
        return obj;
    }
    return obj[key];
}

const handleSearchChange = debounce((newSearchValue) => {
    emit('search', newSearchValue);
}, 250);

function handleSelectOption(opt) {
    emit('update:modelValue', opt);
}

function handleSearchInputClick() {
    if (search.value !== '') {
        search.value = '';
        emit('search', '');
    }
}

function isSelectedOption(option) {
    return (
        getValueFromObject(props.modelValue, props.idKey) ===
        getValueFromObject(option, props.idKey)
    );
}
</script>

<template>
    <Dropdown :width-classes="['w-60']">
        <template
            #trigger="{ isOpen, openDropdown, closeDropdown, toggleDropdown }"
        >
            <slot
                name="trigger"
                :is-open="isOpen"
                :open-dropdown="openDropdown"
                :close-dropdown="closeDropdown"
                :toggle-dropdown="toggleDropdown"
            >
                <Input
                    :value="
                        isOpen
                            ? search
                            : getValueFromObject(modelValue, textKey)
                    "
                    :name="name"
                    type="text"
                    :placeholder="placeholder"
                    class="block w-full"
                    @input="search = $event.target.value"
                    @update:model-value="handleSearchChange"
                    @focus="
                        {
                            handleSearchInputClick();
                            openDropdown();
                        }
                    "
                    @click="handleSearchInputClick"
                />
            </slot>
        </template>
        <template #content>
            <div v-if="options.length">
                <DropdownButton
                    v-for="option in options"
                    :key="getValueFromObject(option, idKey)"
                    class="flex items-center justify-between truncate"
                    @click="handleSelectOption(option)"
                >
                    {{ getValueFromObject(option, textKey) }}
                    <font-awesome-icon
                        v-if="isSelectedOption(option)"
                        class="h-4 w-4"
                        icon="fa-solid fa-check"
                    />
                </DropdownButton>
            </div>
            <DropdownText v-else>No options</DropdownText>
        </template>
    </Dropdown>
</template>
