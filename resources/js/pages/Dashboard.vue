<template>
<MainLayout v-slot="state">
    <ApplicationWindow
        v-for="activity in state.activities"
        :key="`window_${activity.value}`"
        :application="activity" />
</MainLayout>
</template>

<script>
import ApplicationWindow from '@/components/ApplicationWindow.vue';

export default {
    components: {
        ApplicationWindow,
    },

    props: {
        // activities: {
        //     type: Object,
        //     required: true
        // }
    },

    mounted() {
        console.log(this.state);
    }
};
</script>
