import { ref, computed } from 'vue';
import { defineStore, acceptHMRUpdate } from 'pinia';
import axios from 'axios';

export const useActivitiesStore = defineStore('activities', () => {
    // State
    const all = ref(JSON.parse(localStorage.getItem('activities')) || []);
    const active = ref(JSON.parse(localStorage.getItem('active')) || null);
    const dropdown = ref(null);

    // Getters (computed properties)
    const getActiveWindow = computed(() => {
        return all.value.length > 0 ? all.value[all.value.length - 1] : null;
    });

    const getDropdown = computed(() => dropdown.value);

    // Actions (methods)
    const addActivity = (app) => {
        if (!findActivity(app) && app.application) {
            all.value.push(app);
            saveToLocalStorage();
        }
    };

    const removeActivity = (app) => {
        const index = all.value.findIndex(activity => activity === app);
        if (index > -1) {
            all.value.splice(index, 1);
            saveToLocalStorage();
        }
    };

    const findActivity = (app) => {
        console.log('find',app)
        return all?.value.find(activity => activity === app);
    };

    const setActiveWindow = (app) => {
        const index = all.value.indexOf(app);
        if (index > -1) {
            all.value.push(all.value.splice(index, 1)[0]);
            // all.value.unshift(index);
            // active.value = all.value.push(all.value.splice(index, 1)[0]);
            active.value = all.value[0];
            saveToLocalStorage();
        }
    };

    const removeActiveWindow = (app) => {
        const index = all.value.indexOf(app);
        console.log('removing found: ',index,app)
        if (index > -1) {
            console.log('removing',index,app)
            all.value.splice(index, 1);
            all.value.unshift(app);
            saveToLocalStorage();
        }
    };

    const setDropdown = (dropdownValue) => {
        dropdown.value = dropdownValue;
    };

    const saveToLocalStorage = () => {
        localStorage.setItem('activities', JSON.stringify(all.value));
        localStorage.setItem('active', JSON.stringify(active.value)); // Store active window as well
    };

    const loadFromLocalStorage = () => {
        all.value = JSON.parse(localStorage.getItem('activities')) || [];
        active.value = JSON.parse(localStorage.getItem('active')) || null;
    };

    const updateActivityPositions = (app, updatedProperties) => {
        const activity = findActivity(app);
        if (activity) {
            Object.assign(activity, updatedProperties);
            saveToLocalStorage();
        }
    };


    return {
        all,
        active,
        dropdown,
        getActiveWindow,
        getDropdown,
        addActivity,
        removeActivity,
        findActivity,
        setActiveWindow,
        removeActiveWindow,
        setDropdown,
        saveToLocalStorage,
        loadFromLocalStorage,
        updateActivityPositions,
    };
});


if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useActivitiesStore, import.meta.hot));
}
