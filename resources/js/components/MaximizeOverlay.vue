<script setup>
import { computed } from 'vue';
import { useActivitiesStore } from '@/stores/activities';
import { storeToRefs } from 'pinia';

const activitiesStore = useActivitiesStore();
const {
    active,
} = storeToRefs(activitiesStore);

const showOverlay = computed(() => (active?.value && Object.values(active.value.outOfBounds)?.some(val => val === true)) || null);

const fullOverlay = computed(() => active?.value.outOfBounds.y === true);
</script>

<template>
<div
    v-if="showOverlay"
    class="h-screen opacity-50 bg-opacity-50 relative z-30 bg-purple-500 border border-purple-600"
    :style="fullOverlay ? `width: calc(100% - 80px)` : 'width: 50%'" />
</template>