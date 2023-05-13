<template>
<MainLayout>
    <ApplicationWindow
        v-for="activity in all"
        :key="`window_${activity.value}`"
        :application="activity" />
</MainLayout>
</template>

<script>
import { defineComponent } from 'vue';
import activities from '@/state/activities';
import ApplicationWindow from '@/components/ApplicationWindow.vue';

export default defineComponent({
    components: {
        ApplicationWindow,
    },

    setup() {
        const all = activities.state.all;

        return {
            all,
        };
    },
});
</script>
