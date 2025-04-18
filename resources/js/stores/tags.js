import { ref } from 'vue';
import { defineStore, acceptHMRUpdate } from 'pinia';
import axios from 'axios';

export const useTagsStore = defineStore('tags', () => {
    const all = ref(null);
    const active = ref(null);

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
            return axios.get('/api/v1/tags', { params });
        },
        create: (payload) => {
            return axios.post('/api/v1/tags', payload);
        },
        update: (payload) => {
            return axios.patch(`/api/v1/tags/${payload.hash}`, payload);
        },
        destroy: (payload) => {
            return axios.delete(`/api/v1/tags/${payload.hash}`, payload);
        },
    };

    return {
        all,
        active,
        $reset,
        ...actions
    };
});


if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useTagsStore, import.meta.hot));
}
