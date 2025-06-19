<script setup>
import { useActivitiesStore } from '@/stores/activities';
import ApplicationWindow from '@/components/ApplicationWindow.vue';
import MaximizeOverlay from '@/components/MaximizeOverlay.vue';
import { useSettingsStore } from '@/stores/settings';
import { onMounted } from 'vue';
import { storeToRefs } from 'pinia';

const {
    activities,
    active,
} = storeToRefs(useActivitiesStore());

const {
    all,
    fetchUserAgent,
} = useSettingsStore();

onMounted(() => {
    fetchUserAgent();
});
</script>

<template>
<MainLayout>
    <MaximizeOverlay />

    <div
        v-for="activity in activities"
        :id="`window-${activity.data.value}`"
        :ref="`window-${activity.data.value}`"
        :key="`window-${activity.data.value}`">
        <ApplicationWindow
            :activity="activity" />
    </div>
</MainLayout>
</template>
    