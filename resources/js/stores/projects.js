import { ref, inject, computed, reactive, watch } from 'vue';
import { defineStore, acceptHMRUpdate } from 'pinia';
import axios from 'axios';

export const useProjectsStore = defineStore('projects', () => {
    const all = ref(null);

    const fetchProjects = async () => {
        try {
            await axios.get('/api/v1/projects');
            // applications.value = data;
        } catch (error) {
            console.error('Error fetching projects:', error);
        }
    };

    return {
        all,
        fetchProjects
    };
});


if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useProjectsStore, import.meta.hot));
}
