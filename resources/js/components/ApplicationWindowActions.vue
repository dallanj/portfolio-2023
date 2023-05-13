<template>
<ul class="absolute right-2 flex justify-end space-x-1">
    <li
        v-for="action in actions"
        :key="`application-${action.value}-action`"
        class="cursor-pointer rounded-full"
        :class="{ 'hover:bg-app-header-actions-icon': action.value !== 'close' }"
        @click="action.event && action.event()">
        <img
            :src="`/images/icons/actions/${action.icon}`"
            :alt="`${action.label} ${application.label} Application`">
    </li>
</ul>
</template>


<script>
import { defineComponent } from 'vue';
import activities from '@/state/activities';

export default defineComponent({
    props: {
        application: {
            type: Object,
            required: true,
        },
    },

    setup() {
        const removeActivity = activities.removeActivity;

        return {
            removeActivity,
        };
    },

    data() {
        return {
            applicationWindow: null,
            last: {
                x: 0,
                y: 0,
            },
            current: {
                x: 0,
                y: 0,
            },
            cursor: 'cursor-default',
            actions: [
                {
                    label: 'Minimize',
                    value: 'minimize',
                    icon: 'minimize.png',
                },
                {
                    label: 'Maximize',
                    value: 'maximize',
                    icon: 'maximize.png',
                },
                {
                    label: 'Close',
                    value: 'close',
                    icon: 'close.png',
                    event: () => this.removeActivity(this.application),
                },
            ],
        }
    },
});
</script>
