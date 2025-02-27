<script setup>
import { computed, defineProps, defineEmits } from 'vue';

const modelValue = defineModel({
    type: [String, Number, Object],
    required: true,
});

const props = defineProps({
    placeholder: {
        type: String,
        default: 'Search...',
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

const search = () => {
    emit('search');
};

const clearSearch = () => {
    modelValue.value = '';
    search();
};

const alignmentClasses = computed(() => props.sideLabel ? 'flex-row items-center gap-2' : 'flex-col');
</script>

<template>
<div
    class="relative flex"
    :class="alignmentClasses">
    <label v-if="label">{{ label }}</label>
    <div class="relative flex items-center gap-2.5 text-left rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none">
        <input
            v-model="modelValue"
            :class="{
                'rounded-t-lg': isOpen,
                'divrounded-lg': !isOpen,
            }"
            :placeholder="placeholder"
            class="w-full bg-transparent focus:outline-none border-none rounded-l-lg" />
            <button
                v-if="modelValue"
                class="w-8 h-8 flex items-center cursor-pointer"
                @click="clearSearch">
                <FontAwesomeIcon class="fa-fw" size="md" icon="times" />
            </button>
        <button class="w-8 h-8 flex items-center" @click="search">
            <FontAwesomeIcon class="fa-fw" size="md" icon="magnifying-glass" />
        </button>
    </div>
</div>
</template>
