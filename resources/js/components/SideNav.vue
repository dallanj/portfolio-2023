<template>
    <nav class="relative min-h-full h-screen w-20">
        <ul class="nav-menu bg-black bg-opacity-50 border-r border-black scrollbar-hidden overflow-auto h-full p-0.5">
            <li
                v-for="item in menu"
                :key="`nav-${item.value}`"
                :id="`nav-item-${item.value}`"
                class="flex flex-col items-center static p-2 mb-1 rounded-md"
                :class="active === item ? 'bg-white bg-opacity-20 cursor-default hover:bg-opacity-25' : 'cursor-pointer hover:bg-white hover:bg-opacity-10'"
                v-click-outside="select"
                @mouseover="toggleTooltip(item)"
                @mouseout="toggleTooltip(item, false)"
                @click="select(item, true)"
                @click.stop>
                <button
                    class="w-14 relative"
                    :class="active === item ? 'cursor-default' : 'cursor-pointer'">
                    <img
                        :src="`/images/icons/${item.value}.png`"
                        :alt="`${item.label} Application`">
                    <span
                        v-if="activities.find(activity => activity === item)"
                        class="absolute rounded-full active-tab top-1/2 bg-orange" />
                </button>
                <div
                    :id="`tooltip-${item.value}`"
                    class="inline-block whitespace-nowrap transition duration-200 ease-in-out absolute opacity-0 text-topbar-white bg-topbar-grey px-3 py-1 rounded-full z-20 border border-topbar-button-active text-sm">
                        {{ item.label }}
                </div>
            </li>
        </ul>
    </nav>
</template>

<script lang="ts">
import actionsMixin from './../mixins/actionsMixin';

export default {
    mixins: [actionsMixin],

    data() {
        return {
            active: null,
            activities: [],
            menu: [
                {
                    label: 'Terminal',
                    value: 'terminal',
                },
                {
                    label: 'About',
                    value: 'about',
                },
                {
                    label: 'Projects',
                    value: 'projects',
                },
                {
                    label: 'Contact',
                    value: 'contact',
                },
                {
                    label: 'Linked-In',
                    value: 'linkedin',
                },
                {
                    label: 'Github',
                    value: 'github',
                },
                {
                    label: 'Resume',
                    value: 'resume',
                },
            ]
        };
    },

    methods: {
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
}
</script>
