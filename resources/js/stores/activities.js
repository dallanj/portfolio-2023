import { ref, computed } from 'vue';
import { useActivityControls } from '@/composables/useActivityControls';
import { useWindowControls } from '@/composables/useWindowControls';
import { useDrag } from '@/composables/useDrag';
import { defineStore, acceptHMRUpdate } from 'pinia';

export const useActivitiesStore = defineStore('activities', () => {
    const activities = ref([]);
    const active = ref(null);

    const { removeActivity, addActivity, removeAllActivities, addActivities, activityExists } =
        useActivityControls(activities);

    const { maximizeWindow, minimizeWindow, unMaximizeWindow } =
        useWindowControls(activities);

    const { startDrag, onDrag, stopDrag, setCursor } =
        useDrag(activities);

    // State
    const all = ref([]);

    /**
     * Set the active window
     *
     * @param {Object} app
     */
    const windowOutOfBounds = activity => {
        activities.value.push(
            activities.value.splice(
                activities.value.indexOf(activity),
                1
            )[0]
        );
    }

    /**
     * Set the active window
     *
     * @param {Object} app
     */
    const setActiveWindow = activity => {
        const index = activities.value.findIndex(a => a.data.value === activity.value);

        if (index > -1) {
            // Push the activity to the bottom of the array
            activities.value.push(
                activities.value.splice(
                    index,
                    1
                )[0]
            );
            // Set the activity as active
            active.value = activities.value[activities.value.length - 1];
        }
    }

    // Getters (computed properties)
    const getActiveWindow = computed(() => {
        return activities.value.length > 0
            ? activities.value[activities.value.length - 1]
            : null;
    });

    const findActivity = (app) => {
        console.log('find',app)
        return all?.value.find(activity => activity === app);
    };

    const removeActiveWindow = (app) => {
        const index = activities.value.indexOf(app);
        console.log('removing found: ',index,app)
        if (index > -1) {
            console.log('removeActiveWindow',index,app)
            activities.value.splice(index, 1);
            activities.value.unshift(app);
        }

        active.value = activities.value.length > 0
            ? activities.value[activities.value.length - 1]
            : null;
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
        }
    };


    return {
        all,
        active,
        getActiveWindow,
        addActivity,
        removeActivity,
        findActivity,
        setActiveWindow,
        removeActiveWindow,
        updateActivityPositions,

        activities,
        addActivities,
        removeAllActivities,
        activityExists,

        maximizeWindow,
        minimizeWindow,
        unMaximizeWindow,

        startDrag,
        onDrag,
        stopDrag,
        setCursor,

        windowOutOfBounds
    };
});


if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useActivitiesStore, import.meta.hot));
}
