import { ref, inject, computed, reactive, watchEffect } from 'vue';
import { defineStore, acceptHMRUpdate } from 'pinia';
import axios from 'axios';

export const useSettingsStore = defineStore('settings', () => {
    const { openModal } = inject('modals');

    const settings = ref([]);
    const boundaries = reactive({
        left: null, //80,
        top: null,//32,
        bottom: null, //0,
    });
    const settingsMenu = ref(false);
    watchEffect(settingsMenu.value, () => openModal('SettingsMenuModal'));

    const dockPosition = ref('bottom');

    const setBoundaries = () => {
        switch (dockPosition.value) {
            case 'left':
                boundaries.left = 80;
                boundaries.top = 32;
                boundaries.bottom = 0;
                break;
            case 'bottom':
                boundaries.left = 0;
                boundaries.top = 32;
                boundaries.bottom = 80;
                break;
        }
    };

    const setDockPosition = (val) => {
        console.log(val);
        dockPosition.value = val;

        setBoundaries();
    };

    // Open/close settings menu
    const toggleSettingsMenu = () => settingsMenu.value = !settingsMenu.value;

    watchEffect(dockPosition.value, setBoundaries());

    const fetchUserAgent = async () => {
        try {
            // console.log(window.navigator.geolocation.getCurrentPosition('50.65.170.72'));
            const { data } = await axios.get('/api/v1/settings');
            settings.value = data;
        } catch (error) {
            console.error('Error:', error);
        }
    };

    return {
        settings,
        boundaries,
        // boundaryCoords,
        setDockPosition,
        dockPosition,

        toggleSettingsMenu,
        settingsMenu,

        fetchUserAgent,
    };
});


if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useSettingsStore, import.meta.hot));
}
