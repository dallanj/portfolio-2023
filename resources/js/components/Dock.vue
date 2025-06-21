<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick, watch } from 'vue';
import { useSettingsStore } from '@/stores/settings';
import { useDashboardStore } from '@/stores/dashboard';
import { useActivitiesStore } from '@/stores/activities';
import { storeToRefs } from 'pinia';
import { useApplicationVisibility } from '@/composables/useApplicationVisibility.vue';

const dockMenu = ref(null);
let isScrollBound = false;

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

const { activities, active, getActiveWindow } = storeToRefs(activitiesStore);
const {
    setDropdown,
    setActiveWindow,
    addActivity,
    activityExists,
    restoreWindow,
    addActivities,
    removeAllActivities,
} = useActivitiesStore();

// Lifecycle hook: Fetch data and update date label on mount
onMounted(async () => {
    store.updateDateLabel();
    await store.applications();
    isReady.value = true;

    await nextTick();

    // Add event listener for horizonal scroll
    if (dockPosition.value === 'bottom' && dockMenu.value && !isScrollBound) {
        dockMenu.value.addEventListener('wheel', handleHorizontalScroll, { passive: false });
        isScrollBound = true;
    }
});

onBeforeUnmount(() => {
    // Remove event listener for horizonal scroll
    if (dockMenu.value) {
        dockMenu.value.removeEventListener('wheel', handleHorizontalScroll);
    }
});

const handleHorizontalScroll = (e) => {
    // Prevent vertical scroll and scroll dock horizontally instead
    if (e.deltaY !== 0) {
        e.preventDefault();
        dockMenu.value.scrollLeft += e.deltaY;
    }
}

watch(dockPosition, async (newPos, oldPos) => {
    await nextTick();

    if (newPos === 'bottom' && dockMenu.value && !isScrollBound) {
        dockMenu.value.addEventListener('wheel', handleHorizontalScroll, { passive: false });
        isScrollBound = true;
    } else if (oldPos === 'bottom' && dockMenu.value && isScrollBound) {
        dockMenu.value.removeEventListener('wheel', handleHorizontalScroll);
        isScrollBound = false;
    }
});

// Methods
const openApp = (app) => {
    // Hides the tooltip
    toggleTooltip(app, false);

    if (hasClickedOutside(app)) {
        return;
    }

    // Call actionable tabs or applications
    if (active?.action) {
        active?.action();
    }

    const activityId = `${app.value}-activity`;
	const activity = activities.value.find(a => a.id === activityId);
    console.log(activity);

    if (activity) {
		if (activity.minimized) {
            // Restore minimized window animation
			restoreWindow(activity);
		}
	} else {
        // Animate from zoom-in for un-opened apps
		addActivity(app); 
	}

    setActiveWindow(app);
    toggleApplicationVisibility(app);
};

const toggleTooltip = (item, show = true) => {
    const navItem = document.querySelector(`#nav-item-${item.value}`);
    const tooltip = document.querySelector(`#tooltip-${item.value}`);

    if (!tooltip || !navItem) return;

    const navItemPos = navItem.getBoundingClientRect();

    if (show) {
        if (dockPosition.value === 'bottom') {
            const scrollLeft = dockMenu.value.scrollLeft;
            const iconCenter = navItem.offsetLeft + navItem.offsetWidth / 2;
            const tooltipLeft = iconCenter - scrollLeft - tooltip.clientWidth / 2;
            const tooltipTop = navItem.offsetTop - tooltip.clientHeight - 8; // 12px padding above icon

            tooltip.style.left = `${tooltipLeft}px`;
            tooltip.style.top = `${tooltipTop}px`;
        } else {
            tooltip.style.top = `calc(${navItemPos.top}px + ${tooltip.clientHeight / 1.5}px)`;
            tooltip.style.left = `${(navItemPos.left + 4) + navItem.clientWidth}px`;
        }
        tooltip.classList.add('opacity-100', 'visible', 'delay-300', 'z-50');
        tooltip.classList.remove('opacity-0', 'invisible');
    } else {
        tooltip.classList.remove('opacity-100', 'visible', 'delay-300', 'z-50');
        tooltip.classList.add('opacity-0', 'invisible');
    }
}
</script>

<template>
    <!-- Testing Purposes only -->
    <hgroup class="top-20 left-32 absolute block flex flex-col gap-2 border px-3 py-2">
        <h2 class="font-bold text-md underline">Testing Purposes Only</h2>
        <p>
            <b>Dock:</b><br>
            {{ `dock position: ${dockPosition}` }}<br>
            {{ `boundary top: ${boundaries.top}` }}<br>
            {{ `boundary left: ${boundaries.left}` }}<br>
            {{ `boundary bottom: ${boundaries.bottom}` }}<br>
        </p>

        <p class="">
            <b>Activities:</b><br>
            {{ `active window: ${getActiveWindow?.data.label}` }}<br>
            {{ `# of opened windows: ${activities.length}` }}<br>
        </p>
    </hgroup>

    <!-- Dock container -->
    <nav class="z-50"
        :style="dockPosition === 'bottom'
        ? { height: '80px', width: '100%', overflow: 'hidden' }
        : { height: 'calc(100vh - 30px)', width: '80px' }">
        <!-- Dock menu container application items -->
        <menu
            :key="dockPosition"
            ref="dockMenu"
            :class="[
                'nav-menu',
                isReady ? 'dock-ready' : 'dock-hidden',
                dockPosition === 'bottom'
                ? 'flex flex-row items-center h-full w-full overflow-x-auto overflow-y-hidden touch-auto snap-x scroll-smooth whitespace-nowrap'
                : 'flex flex-col overflow-y-auto h-full border-r w-full']">

            <template v-if="isReady">
                <li
                    v-for="app in all"
                    :key="`nav-${app.value}`"
                    :id="`nav-item-${app.value}`"
                    :class="[
                        'p-2 rounded-md',
                        dockPosition === 'bottom' ? 'flex flex-col items-center mr-2' : 'flex flex-col items-center mb-1',
                        getActiveWindow?.data.value === app.value && activityExists(`${app.value}-activity`)
                            ? 'bg-white bg-opacity-20 cursor-default hover:bg-opacity-25'
                            : 'cursor-pointer hover:bg-white hover:bg-opacity-10'
                    ]"
                    @mouseover="toggleTooltip(app)"
                    @mouseout="toggleTooltip(app, false)"
                    @click="openApp(app, true)"
                    @click.stop>
                    <button
                        class="w-14 relative"
                        :class="getActiveWindow === app ? 'cursor-default' : 'cursor-pointer'">
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

<style scoped lang="scss">
.nav-menu {
    @apply bg-black bg-opacity-50 border-black p-0.5 transition-all duration-500;
    scrollbar-width: none;

    li {
        @apply snap-start;
        flex: 0 0 auto; // Prevents flex items from growing or shrinking
    }

    button {
        @apply w-14; // Icon width
    }

    img {
        width: 100%;
        height: auto;
    }

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
