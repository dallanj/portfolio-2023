<script setup>
import { ref, computed, defineProps, defineEmits } from 'vue';

const modelValue = defineModel({
    type: [String, Number, Object],
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
    const selected = props.options.find(
        (opt) => getOptionValue(opt) === props.modelValue
    );
    return selected ? getOptionLabel(selected) : '';
});

const selectOption = (option) => {
    emit('update:modelValue', getOptionValue(option));
    isOpen.value = false;
};

const alignmentClasses = computed(() => props.sideLabel ? 'flex-row items-center gap-2' : 'flex-col');
</script>

<template>
<div
    class="relative flex"
    :class="alignmentClasses">
    <label v-if="label">{{ label }}</label>
    <div>
        <button
            @click="toggleDropdown"
            :class="{
                'rounded-t-lg': isOpen,
                'rounded-lg': !isOpen,
            }"
            class="flex items-center gap-2.5 w-16 px-4 py-1 text-left border-tlr dark:border-gray-800 dark:border-gray-800 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none">
            {{ selectedLabel || placeholder }}
            <FontAwesomeIcon class="fa-fw" size="sm" :icon="isOpen ? 'chevron-up' : 'chevron-down'" />
        </button>
        <ul v-if="isOpen" class="absolute z-50 w-16 -mt-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border-blr dark:border-gray-800 rounded-b-lg">
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
  