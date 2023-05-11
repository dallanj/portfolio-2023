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
export default {
    props: {
        application: {
            type: Object,
            required: true,
        },
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
                    event: () => this.$emit('close-window'),
                },
            ],
        }
    },

    methods: {
        closeWindow() {
            // Todo: Run closing application window animation
            this.$emit('close-window');
        }
    }
};
</script>
