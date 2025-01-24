<template>
<MainLayout>
    <ApplicationWindow
        v-for="activity in all"
        :key="`window_${activity.value}`"
        :activity="activity" />
</MainLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useActivitiesStore } from '@/stores/activities';
import ApplicationWindow from '@/components/ApplicationWindow.vue';

const activitiesStore = useActivitiesStore();
const { all } = useActivitiesStore();
</script>
