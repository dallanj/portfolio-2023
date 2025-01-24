<template>
    <nav class="relative min-h-full h-screen w-20 z-20">
        <ul class="nav-menu bg-black bg-opacity-50 border-r border-black scrollbar-hidden overflow-auto h-full p-0.5">
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
        </ul>
    </nav>
</template>

<script>
import { defineComponent } from 'vue';
import { applications } from '@/state/options';
import activities from '@/state/activities';
import actionsMixin from '@/mixins/actionsMixin';

export default defineComponent({
    mixins: [actionsMixin],

    setup() {
        const all = activities.state.all;
        const active = activities.getActiveWindow;
        const setActiveWindow = activities.setActiveWindow;
        const setDropdown = activities.setDropdown;
        const dropdown = activities.getDropdown;
        const addActivity = activities.addActivity;

        return {
            applications,
            all,
            active,
            dropdown,
            setDropdown,
            setActiveWindow,
            addActivity,
        };
    },

    methods: {
        openApp(app) {
            if (this.hasClickedOutside(app)) {
                this.setDropdown(null);
                return;
            }

            this.setDropdown(null);

            // Call actionable tabs or applications
            if (this.active?.action) {
                this.active?.action();
            }

            this.addActivity(app);
            this.setActiveWindow(app);
            this.toggleApplicationVisibility(app);
        },

        toggleTooltip(item, show = true) {
            const navItem = document.querySelector(`#nav-item-${item.value}`);
            const tooltip = document.querySelector(`#tooltip-${item.value}`);
            
            if (!tooltip || !navItem) {
                return;
            }
            
            const navItemPos = navItem.getBoundingClientRect();

            if (show) {
                tooltip['style'].top = `calc(${navItemPos.top}px - ${tooltip.clientHeight / 3}px)`;
                tooltip['style'].left = `${navItemPos.left + navItem.clientWidth}px`;
                tooltip.classList.add('opacity-100');
                tooltip.classList.add('delay-300');
            } else {
                tooltip.classList.remove('opacity-100');
                tooltip.classList.remove('delay-300');
            }
        }
    }
});
</script>
