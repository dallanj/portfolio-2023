import { ref } from 'vue';
import { defineStore, acceptHMRUpdate } from 'pinia';
import axios from 'axios';
import { router } from '@inertiajs/vue3';

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
        async search(params = {}) {
            const { data } = await axios.get('/api/v1/resumes', { params });
            all.value = data;
            return data;
        },

        async create(payload) {
            const { data } = await axios.post('/api/v1/resumes', payload);
            // Option 1: push new resume to list
            if (all.value) all.value.unshift(data);
            return data;
        },

        async update(payload) {
            const { data } = await axios.patch(`/api/v1/resumes/${payload.hash}`, payload);
            if (all.value) {
                const index = all.value.findIndex(r => r.hash === payload.hash);
                if (index !== -1) all.value[index] = data;
            }
            return data;
        },

        async destroy(payload) {
            console.log(payload)
            await axios.delete(`/api/v1/resumes/${payload.hash}`);
            if (all.value) {
                all.value = all.value.filter(r => r.hash !== payload.hash);
            }
        },

        async bulkDelete(payload) {
            const { data } = await axios.post('/api/v1/resumes/bulk-delete', payload);
            if (all.value) {
                const idsToDelete = new Set(payload.hashes);
                all.value = all.value.filter(r => !idsToDelete.has(r.hash));
            }
            return data;
        },

        async publish(payload) {
            const { data } = await axios.post('/api/v1/resumes/publish', payload);
            // Optionally update the resume if needed
            return data;
        },

        async draft(payload) {
            const { data } = await axios.post('/api/v1/resumes/draft', payload);
            // Optionally update the resume if needed
            return data;
        },

        async show() {
            const response = await axios.get('/api/v1/resumes/show', {
                responseType: 'blob',
            });
            const blob = response.data;
            const url = URL.createObjectURL(blob);
            resume.value = url;
            return url;
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
