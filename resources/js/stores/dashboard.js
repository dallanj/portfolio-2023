// stores/applications.js
import { ref, computed } from 'vue';
import { defineStore, acceptHMRUpdate } from 'pinia';
import axios from 'axios';

export const useDashboardStore = defineStore('dashboard', () => {
    // State
    // const applications = ref(JSON.parse(localStorage.getItem('applications')) || []);

    const applications = ref([]);
    const topBar = ref({
        activities: { label: 'Activities', value: 'activities' },
        current: { label: 'Current Window', value: 'current', action: null },
        date: { label: '', value: 'date' },
        settings: { label: 'Settings', value: 'settings', action: 'toggleSettingsMenu' },
    });

    // Actions
    const fetchApplications = async () => {
        try {
            const { data } = await axios.get('/api/v1/applications');
            applications.value = data;
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
        applications,
        topBar,

        // Actions
        fetchApplications,
        updateDateLabel,

        // Getters
        getApplicationByValue,
    };
});

if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useDashboardStore, import.meta.hot));
}
