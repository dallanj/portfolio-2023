<script setup>
import { computed } from 'vue';

const modelValue = defineModel({
    type: [String, Number],
    required: true,
});

const props = defineProps({
    name: {
        type: String,
        required: true,
    },
    rows: {
        type: String,
        default: '3',
    },
    placeholder: {
        type: String,
        default: '',
    },
    label: {
        type: String,
        default: '',
    },
    sideLabel: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

const alignmentClasses = computed(() =>
    props.sideLabel ? 'flex-row items-center gap-2' : 'flex-col'
);
</script>

<template>
  <div class="flex gap-y-1" :class="alignmentClasses">
    <label v-if="label" class="text-sm font-medium text-gray-700 dark:text-gray-300">
        {{ label }}
    </label>
    <textarea
        v-model="modelValue"
        :id="name"
        :name="name"
        :placeholder="placeholder"
        :disabled="disabled"
        :rows="rows"
        class="w-full px-3 py-2 border border-gray-300 dark:border-app-header-bb rounded-md focus:ring focus:ring-orange focus:outline-none bg-white dark:bg-app-header-bg text-gray-900 dark:text-gray-100"></textarea>
    </div>
</template>
