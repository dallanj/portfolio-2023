<template>
    <section
        class="bg-white rounded-md shadow-md border border-gray-2">
        <header
            v-if="$slots.header"
            class="self-start flex items-center px-4 sm:px-6 py-2 sm:py-3 border-b shadow-sm whitespace-nowrap">
            <h2 class="text-lg font-bold text-brand-dark-gray uppercase">
                <slot name="header" />
            </h2>
            
            <div
                v-if="$slots.actions"
                class="ml-auto">
                <slot name="actions" />
            </div>
        </header>
        <menu
            v-if="$slots.tabs"
            class="self-start flex items-center px-4 sm:px-6 border-b shadow-sm whitespace-nowrap">
            <slot name="tabs" />
        </menu>
        <article class="p-4 sm:p-4 xl:p-6 overflow-y-scroll h-full w-full">
            <slot />
        </article>
    </section>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    hasLoadingSpinner: {
        type: Boolean,
        default: false
    },
});
</script>
