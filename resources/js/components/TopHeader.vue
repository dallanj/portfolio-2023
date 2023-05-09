<template>
    <header class="grid grid-cols-3 w-full h-8 bg-topbar-grey py-1 drop-shadow-md relative z-20">
        <div class="flex col-span-2 md:col-span-1 md:space-x-2">
            <button
                v-for="item in [menu.activities, menu.current]"
                :key="`header-${item.value}`"
                class="px-4 rounded-full transition duration-200 ease-in-out hover:text-white"
                :class="{
                    [activeClass]: activeTab === item,
                    [inactiveClass]: activeTab !== item,
                    'hidden': item === menu.current && !active,
                }"
                v-click-outside="select"
                @click="select(item)"
                @click.stop>
                {{ item === menu.current ? active?.label : item.label }}
            </button>
        </div>

        <button
            v-for="item in [menu.date, menu.settings]"
            :key="`header-${item.value}`"
            class="px-4 rounded-full transition duration-200 ease-in-out hover:text-white"
            :class="{
                [activeClass]: activeTab === item,
                [inactiveClass]: activeTab !== item,
                'justify-self-end md:justify-self-center': item.value !== 'settings',
                'hidden md:block justify-self-end': item.value === 'settings',
            }"
            v-click-outside="select"
            @click="select(item)"
            @click.stop>
            {{ typeof item.label === 'function' ? item.label() : item.label }}
        </button>
    </header>
</template>

<script lang="ts">
import actionsMixin from './../mixins/actionsMixin';

export default {
    mixins: [actionsMixin],
    
    props: {
        activities: {
            type: Array,
            required: true
        },
    },

    data() {
        return {
            activeTab: null,
            menu: {
                activities: {
                    label: 'Activities',
                    value: 'activities',
                },
                current: {
                    label: 'Current Window',
                    value: 'current',
                    action: () => {
                        // TODO: Window options dropdown
                    }
                },
                date: {
                    label: () => {
                        return new Intl.DateTimeFormat([], {
                            timeZone: Intl.DateTimeFormat().resolvedOptions().timeZone,
                            month: 'short',
                            day: 'numeric',
                            hour: 'numeric',
                            minute: 'numeric',
                            hour12: false,
                        }).format(new Date()).replace(/,/g, ' ');
                    },
                    value: 'date',
                },
                settings: {
                    label: 'Settings',
                    value: 'settings',
                }
            }
        };
    },

    computed: {
        active() {
            return this.activities.length > 0 ? this.activities[this.activities.length - 1] : null;
        },

        activeClass() {
            return 'text-white bg-topbar-button hover:bg-topbar-button-active';
        },
        
        inactiveClass() {
            return 'text-topbar-white hover:bg-topbar-button';
        }
    },
}
</script>
