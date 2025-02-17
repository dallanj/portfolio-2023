<template>
    <header>
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

<script setup>
import { computed, inject } from 'vue';
import { topBar } from '@/state/options';
import activities from '@/state/activities';
import actionsMixin from '@/mixins/actionsMixin';
import { useSettingsStore } from '@/stores/settings';
import { storeToRefs } from 'pinia';
const { openModal } = inject('modals');
const {
    setDockPosition,
    toggleSettingsMenu,
    settingsMenu,
} = useSettingsStore();
import { useApplicationVisibility } from '@/composables/useApplicationVisibility.vue';
const { hasClickedOutside, toggleApplicationVisibility, isApplicationVisible } = useApplicationVisibility();

const menu = topBar;
const all = activities.state.all;
const active = activities.getActiveWindow;
const setDropdown = activities.setDropdown;
const dropdown = activities.getDropdown;

const activeClass = computed(() => {
    return 'text-white bg-topbar-button hover:bg-topbar-button-active';
});

const inactiveClass = computed(() => {
    return 'text-topbar-white hover:bg-topbar-button';
});

const selectOption = (option) => {
    // console.log(option.action);
    // setDockPosition();
    if (hasClickedOutside(option)) {
        // toggleSettingsMenu(false);
        return;
    }
    // Select menu option
    // console.log(true, option.action);
    useSettingsStore()[option.action]();
    // action();
    openModal('SettingsMenuModal', {
        position: 'justify-content: flex-end',
    });
    // // Trigger action if applicable
    // if (dropdown?.action) {
    //     dropdown?.action();
    // }
};
</script>
