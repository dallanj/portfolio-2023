import { ref, computed } from 'vue';
import { defineStore, acceptHMRUpdate } from 'pinia';
import axios from 'axios';

export const useSettingsStore = defineStore('settings', () => {
    const settings = ref([]);
    const boundaries = ref(null);

    const fetchUserAgent = async () => {
        try {
            // console.log(window.navigator.geolocation.getCurrentPosition('50.65.170.72'));
            const { data } = await axios.get('/api/v1/settings');
            settings.value = data;
        } catch (error) {
            console.error('Error fetchUserAgent:', error);
        }
    };

    return {
        settings,
        boundaries,

        fetchUserAgent,
    };
});


if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useSettingsStore, import.meta.hot));
}
