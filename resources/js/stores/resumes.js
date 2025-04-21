import { ref } from 'vue';
import { defineStore, acceptHMRUpdate } from 'pinia';
import axios from 'axios';

export const useResumesStore = defineStore('resumes', () => {
    const all = ref(null);
    const active = ref(null);
    const resume = ref(null);

    function $reset(key = null) {
        const resetMap = {
            all: () => { all.value = null; },
            active: () => { active.value = null; },
            resume: () => { resume.value = null; },
        };

        if (key && resetMap[key]) {
            // Reset only the specific key passed
            resetMap[key]();
        }
    };

    const actions = {
        search: (params) => {
            return axios.get('/api/v1/resumes', { params });
        },
        create: (payload) => {
            return axios.post('/api/v1/resumes', payload);
        },
        update: (payload) => {
            return axios.patch(`/api/v1/resumes/${payload.hash}`, payload);
        },
        destroy: (payload) => {
            return axios.delete(`/api/v1/resumes/${payload.hash}`, payload);
        },
        show: async () => {
            // return axios.get(`/api/v1/resumes/show`);
            const response = await axios.get('/api/v1/resumes/show', {
                responseType: 'blob',
            });
            
            const blob = response.data;
            const url = URL.createObjectURL(blob);
            resume.value = url;            
        },
    };

    return {
        all,
        active,
        resume,
        $reset,
        ...actions
    };
});


if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useResumesStore, import.meta.hot));
}
