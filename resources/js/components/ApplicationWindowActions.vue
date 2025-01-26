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
            :alt="`${action.label} ${model.data.label} Application`">
    </li>
</ul>
</template>

<script setup>
import { useActivitiesStore } from '@/stores/activities';

const model = defineModel({
    type: Object,
    required: true,
});

const {
    removeActivity,
    maximizeWindow,
    minimizeWindow,
} = useActivitiesStore();

const actions = [
    {
        label: 'Minimize',
        value: 'minimize',
        icon: 'minimize.png',
        event: () => minimizeWindow(model)
    },
    {
        label: 'Maximize',
        value: 'maximize',
        icon: 'maximize.png',
        event: () => maximizeWindow(model)
    },
    {
        label: 'Close',
        value: 'close',
        icon: 'close.png',
        event: () => removeActivity(model),
    },
];
</script>
