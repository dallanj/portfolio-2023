<script setup>
import { computed, ref, onMounted, onBeforeUnmount } from 'vue';
import { useActivitiesStore } from '@/stores/activities';
import { useSettingsStore } from '@/stores/settings';
import { storeToRefs } from 'pinia';

const { getActiveWindow } = storeToRefs(useActivitiesStore());

const activeOutOfBounds = computed(() => getActiveWindow?.value?.outOfBounds || {});

const { boundaries, dockPosition } =
    useSettingsStore();

const showOverlay = computed(() => (getActiveWindow?.value && Object.values(getActiveWindow.value.outOfBounds)?.some(val => val === true)) || null);

const fullOverlay = computed(() => getActiveWindow?.value.outOfBounds.y === true);

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
    const outOfBounds = activeOutOfBounds.value;

    // Fullscreen overlay for top out-of-bounds (maximize trigger)
    if (outOfBounds.y) {
        return `width: 100%; height: 100vh; left: 0; top: 0; position: absolute;`;
    }

    // Left half overlay for left out-of-bounds
    if (outOfBounds.left) {
        return `width: 50%; height: 100vh; left: 0; top: 0; position: absolute;`;
    }

    // Right half overlay for right out-of-bounds
    if (outOfBounds.right) {
        return `width: 50%; height: 100vh; right: 0; top: 0; position: absolute;`;
    }

    // No overlay
    return '';
});

</script>

<template>
<div
    v-if="showOverlay"
    class="opacity-50 bg-opacity-50 absolute z-30 bg-purple-500 border border-purple-600"
    :style="overlayStyle" />
</template>