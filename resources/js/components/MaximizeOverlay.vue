<script setup>
import { computed } from 'vue';
import { useActivitiesStore } from '@/stores/activities';
import { useSettingsStore } from '@/stores/settings';
import { storeToRefs } from 'pinia';

const activitiesStore = useActivitiesStore();
const {
    active,
} = storeToRefs(activitiesStore);

// const settingsStore = useSettingsStore();
const { boundaries, dockPosition } =
    useSettingsStore();

const showOverlay = computed(() => (active?.value && Object.values(active.value.outOfBounds)?.some(val => val === true)) || null);

const fullOverlay = computed(() => active?.value.outOfBounds.y === true);

const overlayStyle = computed(() => {
    switch (dockPosition) {
        case 'left':
            return `width: calc(${fullOverlay.value ? '100' : '50'}%); height: 100vh`;
            // return fullOverlay.value ? 'height: 100vh;  width: 100%' : 'height: 100vh;  width: 50%';
        case 'bottom':
            return `width: calc(${fullOverlay.value ? '100' : '50'}% - ${fullOverlay.value ? '0' : boundaries.left}px); height: calc(100%)`;
            // return fullOverlay.value ? 'height: 100%;  width: 100%' : 'height: 100%;  width: 50%';
    }
})
</script>

<template>
<div
    v-if="showOverlay"
    class="opacity-50 bg-opacity-50 relative z-30 bg-purple-500 border border-purple-600"
    :style="overlayStyle" />
</template>