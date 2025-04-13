// stores/applications.js
import { ref, computed } from 'vue';
import { defineStore, acceptHMRUpdate } from 'pinia';
import axios from 'axios';

export const useDashboardStore = defineStore('dashboard', () => {
    const all = ref(null);

    const topBar = ref({
        activities: { label: 'Activities', value: 'activities' },
        current: { label: 'Current Window', value: 'current', action: null },
        date: { label: '', value: 'date' },
        settings: { label: 'Settings', value: 'settings', action: 'toggleSettingsMenu' },
    });

    const actions = {
        applications: () => {
            return axios.get('/api/v1/applications');
        },
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

    // Actions
    const fetchApplications = async () => {
        try {
            await axios.get('/api/v1/applications');
        } catch (error) {
            console.error('Error fetching applications:', error);
        }
    };

    const updateDateLabel = () => {
        topBar.value.date.label = new Intl.DateTimeFormat([], {
            timeZone: Intl.DateTimeFormat().resolvedOptions().timeZone,
            month: 'short',
            day: 'numeric',
            hour: 'numeric',
            minute: 'numeric',
            hour12: false,
        })
            .format(new Date())
            .replace(/,/g, ' ');
    };

    // Getters
    const getApplicationByValue = computed(() => (value) =>
        applications.value.find((app) => app.value === value)
    );

    return {
        // State
        all,
        topBar,

        // Actions
        ...actions,
        fetchApplications,
        updateDateLabel,

        // Getters
        getApplicationByValue,
        $reset,
    };
});

if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useDashboardStore, import.meta.hot));
}
