<template>
    <div class="top-20 left-40 absolute block flex flex-col gap-4">
        <p>
            <b>Dock:</b><br>
            {{ `dock position: ${dockPosition}` }}<br>
            {{ `boundary top: ${boundaries.top}` }}<br>
            {{ `boundary left: ${boundaries.left}` }}<br>
            {{ `boundary bottom: ${boundaries.bottom}` }}<br>
        </p>

        <p class="">
            <b>Activities:</b><br>
            {{ `active window: ${active?.data.label}` }}<br>
            {{ `# of opened windows: ${activities.length}` }}<br>
        </p>
    </div>
    <nav>
        <menu
            :class="{ 'dock-ready': isReady, 'dock-hidden': !isReady, 'flex': dockPosition === 'bottom' }"
            class="nav-menu bg-black bg-opacity-50 border-r border-black scrollbar-hidden overflow-auto h-full p-0.5 transition-all duration-500">
            <template v-if="isReady">
                <li
                    v-for="app in all"
                    :key="`nav-${app.value}`"
                    :id="`nav-item-${app.value}`"
                    class="flex flex-col items-center static p-2 mb-1 rounded-md"
                    :class="active?.data.value === app.value && activityExists(`${app.value}-activity`) ? 'bg-white bg-opacity-20 cursor-default hover:bg-opacity-25' : 'cursor-pointer hover:bg-white hover:bg-opacity-10'"
                    @mouseover="toggleTooltip(app)"
                    @mouseout="toggleTooltip(app, false)"
                    @click="openApp(app, true)"
                    @click.stop>
                    <button
                        class="w-14 relative"
                        :class="active === app ? 'cursor-default' : 'cursor-pointer'">
                        <img
                            :src="`/images/icons/apps/${app.value}.png`"
                            :alt="`${app.label} Application`">
                        <span
                            v-if="activityExists(`${app.value}-activity`)"
                            class="absolute rounded-full top-1/2 bg-orange"
                            :class="{
                                'active-tab-left': dockPosition === 'left',
                                'active-tab-bottom': dockPosition === 'bottom',
                            }" />
                    </button>
                    <span
                        :id="`tooltip-${app.value}`"
                        class="select-none pointer-events-none inline-block whitespace-nowrap transition duration-200 ease-in-out absolute opacity-0 text-topbar-white bg-topbar-grey px-3 py-1 rounded-full z-50 border border-topbar-button-active text-sm">
                            {{ app.label }}
                    </span>
                </li>
            </template>
        </menu>
    </nav>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useSettingsStore } from '@/stores/settings';
import { useDashboardStore } from '@/stores/dashboard';
import { useActivitiesStore } from '@/stores/activities';
import { storeToRefs } from 'pinia';
import { useApplicationVisibility } from '@/composables/useApplicationVisibility.vue';

const {
    boundaries,
    dockPosition
} = storeToRefs(useSettingsStore());

const { hasClickedOutside, toggleApplicationVisibility, isApplicationVisible } = useApplicationVisibility();

const isReady = ref(false);
// Use the Pinia store
const store = useDashboardStore();
const { all } = storeToRefs(useDashboardStore());

// Reactistoreve state and getters
const activitiesStore = useActivitiesStore();

const { activities, active } = storeToRefs(activitiesStore);
const {
    setDropdown,
    setActiveWindow,
    addActivity,
    getActiveWindow,
    activityExists,
    
    addActivities,
    removeAllActivities,
} = activitiesStore;

// Lifecycle hook: Fetch data and update date label on mount
onMounted(async () => {
    store.updateDateLabel();
    await store.applications();
    isReady.value = true;
});

// Methods
function openApp(app) {
    if (hasClickedOutside(app)) {
        return;
    }

    // Call actionable tabs or applications
    if (active?.action) {
        active?.action();
    }

    addActivity(app);

    // addActivity(app);
    setActiveWindow(app);
    toggleApplicationVisibility(app);
}

function toggleTooltip(item, show = true) {
    const navItem = document.querySelector(`#nav-item-${item.value}`);
    const tooltip = document.querySelector(`#tooltip-${item.value}`);

    if (!tooltip || !navItem) return;

    const navItemPos = navItem.getBoundingClientRect();

    if (show) {
        tooltip.style.top = `calc(${navItemPos.top}px - ${tooltip.clientHeight / 3}px)`;
        tooltip.style.left = `${navItemPos.left + navItem.clientWidth}px`;
        tooltip.classList.add('opacity-100', 'delay-300');
    } else {
        tooltip.classList.remove('opacity-100', 'delay-300');
    }
}
</script>

<style scoped lang="scss">
.nav-menu {
    @apply relative z-50;
    scrollbar-width: none;
    
    .active-tab-left {
        transform: translateY(-50%);
        left: -7.5px;
        width: 6px;
        height: 6px;
    }

    .active-tab-bottom {
        transform: translate(-50%, -99%);
        top: 65px;
        width: 6px;
        height: 6px;
    }
}
</style>
