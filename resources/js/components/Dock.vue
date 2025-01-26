<template>
    <nav
        class="relative min-h-full h-screen w-20 z-20">
        <ul
            :class="{ 'dock-ready': isReady, 'dock-hidden': !isReady }"
            class="nav-menu bg-black bg-opacity-50 border-r border-black scrollbar-hidden overflow-auto h-full p-0.5 transition-all duration-500">
            <template v-if="isReady">
                <li
                    v-for="app in applications"
                    :key="`nav-${app.value}`"
                    :id="`nav-item-${app.value}`"
                    class="flex flex-col items-center static p-2 mb-1 rounded-md"
                    :class="active === app && isApplicationVisible(app) ? 'bg-white bg-opacity-20 cursor-default hover:bg-opacity-25' : 'cursor-pointer hover:bg-white hover:bg-opacity-10'"
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
                            v-if="all.find(activity => activity === app)"
                            class="absolute rounded-full active-tab top-1/2 bg-orange" />
                    </button>
                    <span
                        :id="`tooltip-${app.value}`"
                        class="select-none pointer-events-none inline-block whitespace-nowrap transition duration-200 ease-in-out absolute opacity-0 text-topbar-white bg-topbar-grey px-3 py-1 rounded-full z-20 border border-topbar-button-active text-sm">
                            {{ app.label }}
                    </span>
                </li>
            </template>
        </ul>
    </nav>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useDashboardStore } from '@/stores/dashboard';
import { useActivitiesStore } from '@/stores/activities';
import { storeToRefs } from 'pinia';
import { useApplicationVisibility } from '@/composables/useApplicationVisibility.vue';

const { hasClickedOutside, toggleApplicationVisibility, isApplicationVisible } = useApplicationVisibility();

const isReady = ref(false);
// Use the Pinia store
const store = useDashboardStore();

const { applications } = storeToRefs(store);

// Reactive state and getters
const activitiesStore = useActivitiesStore();

const { activities, all, active } = storeToRefs(activitiesStore);
const {
    setDropdown,
    setActiveWindow,
    addActivity,
    getActiveWindow,
    
    addActivities,
    removeAllActivities,
} = activitiesStore;

// Lifecycle hook: Fetch data and update date label on mount
onMounted(async () => {
    store.updateDateLabel();
    await store.fetchApplications();
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
