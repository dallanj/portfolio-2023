import { ref } from 'vue';
import { defineStore, acceptHMRUpdate } from 'pinia';
import axios from 'axios';

export const useContactsStore = defineStore('contacts', () => {
    const all = ref(null);
    const active = ref(null);
    const publicKey = ref(null);

    function $reset(key = null) {
        const resetMap = {
            all: () => { all.value = null; },
            active: () => { active.value = null; },
            publicKey: () => { publicKey.value = null; },
        };

        if (key && resetMap[key]) {
            // Reset only the specific key passed
            resetMap[key]();
        }
    };

    const actions = {
        search: (params) => {
            return axios.get('/api/v1/contacts', { params });
        },
        create: (payload) => {
            return axios.post('/api/v1/contacts', payload);
        },
        show: (params) => {
            return axios.post(`/api/v1/contacts/${params.hash}`, params);
        },
        getPublicKey: () => {
            return axios.get('/api/v1/contacts/pgp');
        },

        markRead: (payload) => {
            return axios.post('/api/v1/contacts/mark-read', payload);
        },
        markUnread: (payload) => {
            return axios.post('/api/v1/contacts/mark-unread', payload);
        },
        markImportant: (payload) => {
            return axios.post('/api/v1/contacts/mark-important', payload);
        },
        bulkDelete: (payload) => {
            return axios.post('/api/v1/contacts/bulk-delete', payload);
        },
    };

    return {
        all,
        active,
        publicKey,
        $reset,
        ...actions
    };
});


if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useContactsStore, import.meta.hot));
}
