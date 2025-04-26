import { ref, inject, computed, reactive, watch } from 'vue';
import { defineStore, acceptHMRUpdate } from 'pinia';
import axios from 'axios';

export const useProjectsStore = defineStore('projects', () => {
    const all = ref(null);
    const active = ref(null);
    const history = ref([]);
    const future = ref([]);

    function $reset(key = null) {
        const resetMap = {
            all: () => { all.value = null; },
            active: () => { active.value = null; },
        };

        if (key && resetMap[key]) {
            // Reset only the specific key passed
            resetMap[key]();
        }
    };

    const actions = {
        search: (params) => {
            return axios.get('/api/v1/projects', { params });
        },
        create: (payload) => {
            return axios.post('/api/v1/projects', payload);
        },
        update: (payload) => {
            return axios.patch(`/api/v1/projects/${payload.hash}`, payload);
        },
        destroy: (payload) => {
            return axios.delete(`/api/v1/projects/${payload.hash}`, payload);
        },
        setActive(project) {
            if (active.value?.id === project?.id) return;

            if (active.value !== null) history.value.push(active.value);
            future.value = [];
            active.value = project;
        },
    };

    return {
        all,
        active,
        history,
        future,
        $reset,
        ...actions
    };
});


if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useProjectsStore, import.meta.hot));
}
