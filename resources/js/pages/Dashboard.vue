<template>
<MainLayout>
    <MaximizeOverlay />

    <ApplicationWindow
        v-for="activity in activities"
        :ref="`window-${activity.data.value}`"
        :key="`window-${activity.data.value}`"
        :activity="activity" />
</MainLayout>
</template>

<script setup>
import { useActivitiesStore } from '@/stores/activities';
import ApplicationWindow from '@/components/ApplicationWindow.vue';
import MaximizeOverlay from '@/components/MaximizeOverlay.vue';
import { useSettingsStore } from '@/stores/settings';
import { onMounted } from 'vue';

const {
    activities,
} = useActivitiesStore();

const {
    settings,
    fetchUserAgent,
} = useSettingsStore();

onMounted(() => {
    fetchUserAgent();
});
</script>
