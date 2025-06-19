<script setup>
import { computed, ref, inject } from 'vue';
import { storeToRefs } from 'pinia';
import { useActivitiesStore } from '@/stores/activities';
import { useSettingsStore } from '@/stores/settings';
import { useLiveDateTime } from '@/composables/useLiveDateTime';

const { openModal } = inject('modals');
const { isMobile, isTablet } = inject('screenSize');

// Setup stores
const activitiesStore = useActivitiesStore();
const settingsStore = useSettingsStore();

// Destructure with aliases
const { getActiveWindow } = storeToRefs(activitiesStore);

// Activity store functions
const {
    activityExists,
} = useActivitiesStore();

const {
    setDockPosition,
    toggleSettingsMenu,
    settingsMenu,
    dockPosition
} = useSettingsStore();

const { dateTime } = useLiveDateTime({
    timeZone: Intl.DateTimeFormat().resolvedOptions().timeZone,
    month: 'short',
    day: 'numeric',
    hour: 'numeric',
    minute: 'numeric',
    hour12: false,
});

const showOpenedActivities = () => {
    console.log('zoom out animation and shows all opened windows, clickable to make it the active project');
};

const toggleSettings = () => {
    openModal('SettingsMenuModal', {
        position: 'justify-content: flex-end; align-items: flex-start; padding-top: 10px;/',
    });
}
</script>

<template>
<header>
    <div class="flex items-center space-x-1 justify-start">
        <!-- Activities button (Zoom out animation and shows all opened windows, clickable to make it the active project)-->
        <SimpleButton
            state="topbar"
            :uppercase="false"
            size="topbar"
            @click="showOpenedActivities"
            @click.prevent>
            Activities
        </SimpleButton>

        <!-- Active project name -->
        <span class="px-4 cursor-pointer rounded-full transition duration-200 ease-in-out text-white bg-topbar-span hover:bg-topbar-button-active">
            {{ getActiveWindow?.data.label }}
        </span>
    </div>

    <!-- Date and time (not shown on mobile) -->
    <div
        v-if="!(isMobile || isTablet)"
        class="flex items-center justify-center">
        <span class="px-4 cursor-pointer rounded-full transition duration-200 ease-in-out text-white bg-topbar-span hover:bg-topbar-button-active">
            {{ dateTime }}
        </span>
    </div>

    <!-- Settings menu (cog icon when mobile or screen size is small) -->
    <div class="flex items-center justify-end">
        <SimpleButton
            state="topbar"
            :uppercase="false"
            size="topbar"
            :icon="isMobile ? 'gear' : false"
            @click="toggleSettings"
            @click.prevent>
            {{ isMobile ? '' : 'Settings' }}
        </SimpleButton>
    </div>
</header>
</template>
