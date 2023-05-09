<template>
<ul class="absolute right-2 flex justify-end">
    <li
        class="cursor-pointer"
        @click="closeWindow">
        <img
            :src="`/images/icons/actions/close.png`"
            :alt="`Close ${application.label} Application`">
    </li>
</ul>
</template>


<script lang="ts">
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
