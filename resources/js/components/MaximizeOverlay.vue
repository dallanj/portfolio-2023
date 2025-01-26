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
    class="h-screen bg-opacity-50 relative z-30 bg-purple-600 border border-purple-300"
    :class="fullOverlay ? 'w-full' : 'w-1/2'" />
</template>