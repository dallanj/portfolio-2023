import { defineStore, acceptHMRUpdate } from 'pinia';
import { ref } from 'vue';

export const useUserStore = defineStore('user', () => {
    const all = ref(null)
    const profile = ref(null)

    function $reset(key = null) {
        const resetMap = {
            all: () => { all.value = null; },
            profile: () => { profile.value = null; },
        };

        if (key && resetMap[key]) {
            // Reset only the specific key passed
            resetMap[key]();
        } else {
            // Reset all if no key or invalid key is provided
        }
    }

    const actions = {
        searchUser: (params) => {
            return axios.get('/api/v1/users', { params });
        },
        createUser: (payload) => {
            return axios.post('/api/v1/users', payload);
        },
        updateUser: (payload) => {
          return axios.patch(`/api/v1/users/${payload.hash}`, payload);
        },
    };

    return {
        all,
        profile,
        $reset,
        ...actions
    };
});

// HMR
if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useUserStore, import.meta.hot))
}
