import { ref, inject, computed, reactive, watch } from 'vue';
import { defineStore, acceptHMRUpdate } from 'pinia';
import axios from 'axios';

export const useSettingsStore = defineStore('settings', () => {
    const { openModal } = inject('modals');

    const all = ref([]);
    const boundaries = reactive({
        left: null, //80,
        top: null,//32,
        bottom: null, //0,
    });
    const settingsMenu = ref(false);
    watch(settingsMenu.value, () => openModal('SettingsMenuModal'));

    const dockPosition = ref('left');

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

    watch(dockPosition.value, setBoundaries());

    const fetchUserAgent = async () => {
        try {
            await axios.get('/api/v1/settings');
        } catch (error) {
            console.error('Error:', error);
        }
    };

    function $reset(key = null) {
        const resetMap = {
            all: () => { all.value = [0]; },
        };

        if (key && resetMap[key]) {
            // Reset only the specific key passed
            resetMap[key]();
        }
    };

    return {
        all,
        boundaries,
        // boundaryCoords,
        setDockPosition,
        dockPosition,

        toggleSettingsMenu,
        settingsMenu,

        fetchUserAgent,
        $reset,
    };
});


if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useSettingsStore, import.meta.hot));
}
