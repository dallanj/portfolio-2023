<template>
<div class="simple-loading-spinner" :class="container">
    <div class="simple-loading-spinner__container">
        <div class="simple-loading-spinner__spinner">
            <div class="simple-loading-spinner__icon" :class="sizing" />
        </div>
    </div>
</div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    size: {
        type: String,
        default: 'lg',
        validator: value => ['sm', 'md', 'lg', 'xl', '2xl'].includes(value)
    },
    message: {
        type: String,
        required: false,
    }
});

const { size, message } = props;

const sizing = computed(_ => {
    return {
        sm: 'w-9 h-9 border-4',
        md: 'w-12 h-12 border-6',
        lg: 'w-16 h-16 border-8',
        xl: 'w-20 h-20 border-10',
        '2xl': 'w-24 h-24 border-12',
    }[size];
});

const container = computed(_ => {
    return {
        sm: 'my-2',
        md: 'my-4',
        lg: 'py-6',
        xl: 'my-10',
        '2xl': 'my-12',
    }[size];
});
</script>
    