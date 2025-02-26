import { ref, inject, computed, reactive, watch } from 'vue';
import { defineStore, acceptHMRUpdate } from 'pinia';
import axios from 'axios';

export const useProjectsStore = defineStore('projects', () => {
    const all = ref(null);

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
    }

    return {
        all,
        ...actions
    };
});


if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useProjectsStore, import.meta.hot));
}
