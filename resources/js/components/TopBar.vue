<template>
    <header class="grid grid-cols-3 w-full h-8 bg-topbar-grey py-1 drop-shadow-md relative z-50">
        <div class="flex col-span-2 md:col-span-1 md:space-x-2">
            <button
                v-for="option in [menu.activities, menu.current]"
                :key="`header-${option.value}`"
                class="px-4 rounded-full transition duration-200 ease-in-out hover:text-white"
                :class="{
                    [activeClass]: dropdown === option,
                    [inactiveClass]: dropdown !== option,
                    'hidden': option === menu.current && (!active || !isApplicationVisible(active)),
                }"
                @click="selectOption(option)"
                @click.stop>
                {{ option === menu.current ? active?.label : option.label }}
            </button>
        </div>

        <button
            v-for="option in [menu.date, menu.settings]"
            :key="`header-${option.value}`"
            class="px-4 rounded-full transition duration-200 ease-in-out hover:text-white"
            :class="{
                [activeClass]: dropdown === option,
                [inactiveClass]: dropdown !== option,
                'justify-self-end md:justify-self-center': option.value !== 'settings',
                'hidden md:block justify-self-end': option.value === 'settings',
            }"
            @click="selectOption(option)"
            @click.stop>
            {{ typeof option.label === 'function' ? option.label() : option.label }}
        </button>
    </header>
</template>

<script>
import { defineComponent } from 'vue';
import { topBar } from '@/state/options';
import activities from '@/state/activities';
import actionsMixin from '@/mixins/actionsMixin';

export default defineComponent({
    mixins: [actionsMixin],

    setup() {
        const menu = topBar;
        const all = activities.state.all;
        const active = activities.getActiveWindow;
        const setDropdown = activities.setDropdown;
        const dropdown = activities.getDropdown;

        return {
            menu,
            all,
            active,
            setDropdown,
            dropdown,
            activities
        };
    },

    computed: {
        activeClass() {
            return 'text-white bg-topbar-button hover:bg-topbar-button-active';
        },
        
        inactiveClass() {
            return 'text-topbar-white hover:bg-topbar-button';
        }
    },


    methods: {
        selectOption(option) {
            if (this.hasClickedOutside(option)) {
                this.setDropdown(null);
                return;
            }

            // Select menu option
            this.setDropdown(option);

            // Trigger action if applicable
            if (this.dropdown?.action) {
                this.dropdown?.action();
            }
        },
    }
});
</script>
