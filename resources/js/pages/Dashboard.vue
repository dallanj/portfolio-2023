<template>
<MainLayout v-slot="state">
    <ApplicationWindow
        v-for="activity in state.activities"
        :key="`window_${activity.value}`"
        :application="activity"
        :activities="state.activities" />
</MainLayout>
</template>

<script>
import ApplicationWindow from '@/components/ApplicationWindow.vue';

export default {
    components: {
        ApplicationWindow,
    },
};
</script>
