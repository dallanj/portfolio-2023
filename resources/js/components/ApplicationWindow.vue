<script setup>
import { ref, watchEffect } from 'vue';
import { useActivitiesStore } from '@/stores/activities';
import { useResize } from '@/composables/useResize';
import ApplicationWindowHeader from './ApplicationWindowHeader.vue';
import ApplicationWindowActions from './ApplicationWindowActions.vue';

const props = defineProps({
    activity: {
        type: Object,
        required: true,
    },
});

const application = ref(props.activity);

// Resize composable for the application window
const {
    cursor,
    windowWidth,
    windowHeight,
    startActions,
    setCursor
} = useResize(application);

// Activity store functions
const {
    getActiveWindow,
    activityExists,
} = useActivitiesStore();

const updated = (event) => {
    // Apply animations
}

watchEffect(application.value, updated);
</script>

<template>
<article
    :ref="`${application.data.value}-application`"
    :id="`${application.data.value}-application`"
    class="app-window fixed block"
    :class="[
      cursor,
      { 'rounded-t-xl': application.roundedBorder },
      { 'z-40': application === getActiveWindow && Object.values(application.outOfBounds).some(val => val === true) }
    ]"
    :style="{
        width: windowWidth,
        height: windowHeight,
        top: application.top + 'px',
        left: application.left + 'px'
    }"
    @mousedown="startActions"
    @mouseup="stopResize"
    @mousemove="setCursor">

    <ApplicationWindowHeader v-model="application">
        <h2 class="select-none">{{ application.data.label }}</h2>
        <ApplicationWindowActions v-model="application" />
    </ApplicationWindowHeader>
</article>
</template>
