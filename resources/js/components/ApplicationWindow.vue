<script setup>
import { ref, watch } from 'vue';
import { storeToRefs } from 'pinia';
import { useActivitiesStore } from '@/stores/activities';
import { useResize } from '@/composables/useResize';
import ApplicationWindowHeader from './ApplicationWindowHeader.vue';
import ApplicationWindowActions from './ApplicationWindowActions.vue';
import Projects from '@/Pages/Dashboard/Projects.vue';
import Resume from '@/Pages/Dashboard/Resume.vue';
import { useProjectsStore } from '@/stores/projects';
import { useTagsStore } from '@/stores/tags';

// Setup stores
const projectsStore = useProjectsStore();
const tagsStore = useTagsStore();

// Destructure with aliases
const { active: activeProject } = storeToRefs(projectsStore);
const { active: activeTag } = storeToRefs(tagsStore);

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

watch(application.value, updated);

const projectsRef = ref(null);
</script>

<template>
<article
    :ref="`${application.data.value}-application`"
    :id="`${application.data.value}-application`"
    class="app-window fixed block flex flex-col overflow-hidden"
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
        <div v-if="application.id === 'projects-activity'" class="absolute left-2 flex justify-end space-x-1px bg-topbar-nav-bg rounded border border-topbar-nav-bg">
            <button
                class="w-7 h-7 rounded-l"
                :class="[
                    (activeTag || activeProject) ? 'text-white bg-topbar-button-active' : 'text-gray-500 bg-topbar-button-inactive'
                ]"
                :disabled="!tagsStore.history.length && !projectsStore.history.length"
                @click="projectsRef?.handleBackClick?.()"
                title="Back">
                <FontAwesomeIcon class="fa-fw" icon="chevron-left" size="sm" />
                </button>

            <button
                class="w-7 h-7 rounded-r"
                :class="[
                    (activeTag || activeProject) ? 'text-white bg-topbar-button-active' : 'text-gray-500 bg-topbar-button-inactive'
                ]"
                :disabled="!tagsStore.future.length && !projectsStore.future.length"
                @click="projectsRef?.handleForwardClick?.()"
                title="Forward">
                <FontAwesomeIcon class="fa-fw" icon="chevron-right" size="sm" />
            </button>
        </div>
        <hgroup class="flex space-x-3">
            <h2 class="select-none">{{ application.data.label }}</h2>
            <p v-if="activeTag" class="space-x-3">
                <span class="space-x-3">/</span>
                <span>{{ activeTag?.name }}</span>
            </p>
            <p v-if="activeProject" class="space-x-3">
                <span class="space-x-3">/</span>
                <span>{{ activeProject?.title }}</span>
            </p>
        </hgroup>
        <ApplicationWindowActions v-model="application" />
    </ApplicationWindowHeader>
    
    <section v-if="application.id === 'projects-activity'" class="flex-1 overflow-hidden">
        <Projects ref="projectsRef" />
    </section>

    <section v-if="application.id === 'terminal-activity'">
        terminal
    </section>

    <section v-if="application.id === 'resume-activity'">
        <Resume />
    </section>

    <section v-if="application.id === 'about-activity'">
        about
    </section>

    <section v-if="application.id === 'contact-activity'">
        contact
    </section>
</article>
</template>
