<template>
    <div>
        <TopHeader />

        <div class="flex">
            <SideNav :activities="state.activities" />
            <slot :activities="state.activities" />
        </div>
    </div>
</template>

<script>
import { reactive } from 'vue';
import TopHeader from '@/components/TopHeader.vue';
import SideNav from '@/components/SideNav.vue';

export default {
    components: {
        TopHeader,
        SideNav
    },

    setup() {
        const state = reactive({
            activities: []
        });

        return {
            state
        };
    }
};
</script>
