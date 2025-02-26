<template>
<button
    class="inline-flex items-center border border-transparent transition ease-in-out duration-150"
    :class="[
        buttonClasses,
        { 'uppercase': uppercase },
    ]"
    :disabled="isDisabled">
    <span
        v-if="icon && iconPosition === 'prefix'"
        :class="iconClasses">
        <FontAwesomeIcon :icon="icon" :class="{ 'fa-spin': iconSpin }" />
    </span>
    <slot />
    <span v-if="icon && iconPosition === 'suffix'" :class="iconClasses">
        <FontAwesomeIcon :icon="icon" :class="{ 'fa-spin': iconSpin }" />
    </span>
</button>
</template>


<script setup>
import { computed, useAttrs } from 'vue';

const props = defineProps({
    size: {
        type: String,
        default: 'medium',
        validator: value => ['small', 'medium', 'large', 'action'].includes(value)
    },
    state: {
        type: String,
        default: 'primary',
        validator: value => ['primary', 'secondary', 'moving', 'plain', 'action'].includes(value)
    },
    icon: {
        type: String,
        default: ''
    },
    iconPosition: {
        type: String,
        default: 'prefix'
    },
    iconSpin: {
        type: Boolean,
        default: false,
    },
    isDisabled: {
        type: Boolean,
        default: false
    },
    uppercase: {
        type: Boolean,
        default: true
    },
});

// Composables
import { useScreenSize } from '@/composables/useScreenSize.vue';
const { isMobile } = useScreenSize();

const attrs = useAttrs();

const hasWidth = computed(_ => {
    return attrs.width !== undefined;
});

const buttonClasses = computed(_ => {
    let classes = 'inline-flex items-center border border-transparent';

    if (hasWidth.value && isMobile.value) {
        classes += ` ${attrs.width}`;
    }

    // Add size classes
    switch (props.size) {
        case 'action':
            classes += ' text-sm';
            break;
        case 'small':
            classes += ' px-1 sm:px-2 py-1 text-xs';
            break;
        case 'medium':
            classes += ' px-2 sm:px-4 py-2 text-sm';
            break;
        case 'large':
            classes += ' px-4 sm:px-6 py-3 text-lg';
            break;
    }

    // Add state classes
    switch (props.state) {
        case 'primary':
            classes += ' bg-blue-200 text-white hover:bg-blue-200/[.80] focus:bg-blue-200/[.80] active:bg-blue-200 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 font-semibold rounded-md tracking-widest';
            break;
        case 'removing':
            classes += ' bg-red-500 text-white hover:bg-red-400 focus:bg-red-400 active:bg-red-600 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 font-semibold rounded-md tracking-widest';
            break;
        case 'secondary':
            classes += ' bg-gray-500 text-white hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-600 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 font-semibold rounded-md tracking-widest';
            break;
        case 'plain':
            classes += ' text-gray-500 rounded-md font-semibold tracking-widest';
            break;
        case 'action':
            classes += ' text-gray-900 dark:text-gray-100';
            break;
    }

    return classes;
});

const iconClasses = computed(() => {
    return props.icon ? 'mr-2 fa-fw' : '';
});
</script>
    
    