import { ref, inject, reactive, watch } from 'vue';
import { defineStore, acceptHMRUpdate } from 'pinia';
import { useScreenSize } from '@/composables/useScreenSize.vue'; // Update path as needed

export const useSettingsStore = defineStore('settings', () => {
    const { openModal } = inject('modals');
    const { isMobile, isTablet } = useScreenSize();

    const all = ref([]);
    const boundaries = reactive({
        left: null,
        top: null,
        bottom: null,
    });

    const dockPosition = ref('left');
    const settingsMenu = ref(false);

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
        dockPosition.value = val;
        setBoundaries();
    };

    // Toggle modal when settingsMenu opens
    watch(settingsMenu, () => openModal('SettingsMenuModal'));

    // Watch dockPosition for boundary update
    watch(dockPosition, setBoundaries);

    // Automatically set dock position for mobile/tablet
    watch(isMobile, (mobile) => {
        setDockPosition(mobile ? 'bottom' : 'left');
    }, { immediate: true });

    const toggleSettingsMenu = () => {
        settingsMenu.value = !settingsMenu.value;
    };

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
            resetMap[key]();
        }
    };

    return {
        all,
        boundaries,
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
