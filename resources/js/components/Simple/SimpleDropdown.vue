<script setup>
import { ref, computed, defineProps, defineEmits } from 'vue';

const modelValue = defineModel({
    type: [String, Number, Object, Array],
    required: true,
});

const props = defineProps({
    options: {
        type: Array,
        required: true,
    },
    placeholder: {
        type: String,
        default: 'Select an option',
    },
    labelKey: {
        type: String,
        default: 'label',
    },
    valueKey: {
        type: String,
        default: 'value',
    },
    label: {
        type: String,
        default: '',
    },
    sideLabel: {
        type: Boolean,
        default: false,
    },
    multiple: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['update:modelValue']);
const isOpen = ref(false);

const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
};

const getOptionLabel = (option) =>
    typeof option === 'object' ? option[props.labelKey] : option;

const getOptionValue = (option) =>
    typeof option === 'object' ? option[props.valueKey] : option;

const selectedLabel = computed(() => {
    if (props.multiple && Array.isArray(props.modelValue)) {
        const selectedLabels = props.options
            .filter(opt => props.modelValue.includes(getOptionValue(opt)))
            .map(getOptionLabel);
        return selectedLabels.join(', ');
    } else {
        const selected = props.options.find(
            opt => getOptionValue(opt) === props.modelValue
        );
        return selected ? getOptionLabel(selected) : '';
    }
});

const selectOption = (option) => {
    const optionValue = getOptionValue(option);
    if (props.multiple) {
        const current = Array.isArray(props.modelValue) ? [...props.modelValue] : [];
        const index = current.indexOf(optionValue);
        if (index >= 0) {
            current.splice(index, 1);
        } else {
            current.push(optionValue);
        }
        emit('update:modelValue', current);
    } else {
        emit('update:modelValue', optionValue);
        isOpen.value = false;
    }
};

const alignmentClasses = computed(() => props.sideLabel ? 'flex-row items-center gap-2' : 'flex-col');
</script>

<template>
<div
    class="relative flex space-y-1"
    :class="alignmentClasses">
    <label v-if="label">{{ label }}</label>
    <div class="relative">
        <button
            type="button"
            @click="toggleDropdown"
            :class="{
                'rounded-t-lg': isOpen,
                'rounded-lg': !isOpen,
            }"
            class="w-full justify-between flex items-center gap-2.5 w-16 px-4 py-2 rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600">
            {{ selectedLabel || placeholder }}
            <FontAwesomeIcon class="fa-fw" size="sm" :icon="isOpen ? 'chevron-up' : 'chevron-down'" />
        </button>
        <ul
            v-if="isOpen"
            class="w-full absolute z-50 -mt-1 bg-white border-gray-300 
            dark:border-gray-700 dark:text-gray-300 text-gray-900 dark:text-gray-100 
            border-blr rounded-b-lg rounded-md border border-gray-300 
            shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
            <div class="overflow-hidden">
                <li
                    v-for="option in options"
                    :key="getOptionValue(option)"
                    @click="selectOption(option)"
                    class="px-4 py-2 cursor-pointer hover:bg-gray-100 hover:dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    {{ getOptionLabel(option) }}
                </li>
            </div>
        </ul>
    </div>
</div>
</template>
  