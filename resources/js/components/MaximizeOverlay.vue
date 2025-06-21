<script setup>
import { computed, ref, onMounted, onBeforeUnmount } from 'vue';
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

const topBarHeight = ref(0);

const calculateTopBarHeight = () => {
    const el = document.getElementById('top-bar');
    
    if (el) {
        topBarHeight.value = el.getBoundingClientRect().height;
    }
};

onMounted(() => {
    calculateTopBarHeight();
    window.addEventListener('resize', calculateTopBarHeight);
});

onBeforeUnmount(() => {
    window.removeEventListener('resize', calculateTopBarHeight);
});

const overlayStyle = computed(() => {
    switch (dockPosition) {
        case 'left':
            return `width: calc(${fullOverlay.value ? '100' : '50'}%); height: 100vh`;
        case 'bottom':
            const width = fullOverlay.value
                ? '100%'
                : `calc(50% - ${boundaries.left}px)`;
            const height = `calc(100vh - ${topBarHeight.value}px - ${boundaries.bottom}px)`;
            return `width: ${width}; height: ${height}`;
    }
});
</script>

<template>
<div
    v-if="showOverlay"
    class="opacity-50 bg-opacity-50 absolute z-30 bg-purple-500 border border-purple-600"
    :style="[overlayStyle, dockPosition === 'bottom' ? { top: `${topBarHeight}px` } : {}]" />
</template>