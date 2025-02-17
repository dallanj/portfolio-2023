<template>
<div class="simple-modal">
    <div class="simple-modal__container" :class="`simple-modal__size-${modalSize}`">
        <!-- <header v-if="$slots.header" class="">
            <hgroup class="simple-modal__hgroup">
                <div class="flex justify-between">
                    <h2
                        class="font-bold"
                        :class="`simple-modal__hgroup-text-${headerSize}`">
                        <slot name="header" />
                    </h2>
                    <button class="simple-modal__hgroup-exit">
                        <FontAwesomeIcon icon="times" size="xl" @click="$emit('close')" />
                    </button>
                </div>
                <p
                    v-if="$slots.subtitle"
                    class="simple-modal__hgroup-subtitle"
                    :class="`simple-modal__hgroup-text-${subtitleSize}`">
                    <slot name="subtitle" />
                </p>
            </hgroup>
        </header> -->
        <section class="simple-modal__section">
            <slot />
        </section>
        <footer v-if="$slots.footer" class="simple-modal__footer">
            <slot name="footer" />
        </footer>
    </div>
</div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    modalSize: {
        type: String,
        default: 'base',
        validator: value => ['sm', 'md', 'base', 'lg', 'xl'].includes(value)
    },

    headerSize: {
        type: String,
        default: '2xl',
        validator: value => ['base', 'lg', 'xl', '2xl'].includes(value)
    },
});

// Auto size the subtitle 1 size lower than the header size
const subtitleSize = computed(_ => {
    const sizeMap = ['base', 'lg', 'xl', '2xl'];
    const headerIndex = sizeMap.indexOf(props.headerSize);
    // Ensure we don't go below 'base'
    return sizeMap[Math.max(0, headerIndex - 2)];
});
</script>