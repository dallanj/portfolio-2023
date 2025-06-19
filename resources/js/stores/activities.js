import { ref, computed, nextTick } from 'vue';
import { useActivityControls } from '@/composables/useActivityControls';
import { useWindowControls } from '@/composables/useWindowControls';
import { useDrag } from '@/composables/useDrag';
import { defineStore, acceptHMRUpdate } from 'pinia';

export const useActivitiesStore = defineStore('activities', () => {
    const activities = ref([]);
    const active = ref(null);

    const { removeActivity, addActivity, removeAllActivities, addActivities, activityExists } =
        useActivityControls(activities);

    const { maximizeWindow, minimizeWindow, unMaximizeWindow, restoreWindow } =
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
        for (let i = activities.value.length - 1; i >= 0; i--) {
            const activity = activities.value[i];
            if (!activity.minimized) return activity;
        }
        return null;
    });

    const findActivity = (app) => {
        return all?.value.find(activity => activity === app);
    };

    const removeActiveWindow = (app) => {
        const index = activities.value.indexOf(app);
        if (index > -1) {
            activities.value.splice(index, 1);
            activities.value.unshift(app);
        }

        active.value = activities.value.length > 0
            ? activities.value[activities.value.length - 1]
            : null;
    };

    const updateActivityPositions = (app, updatedProperties) => {
        const activity = findActivity(app);
        if (activity) {
            Object.assign(activity, updatedProperties);
        }
    };

    function $reset(key = null) {
        const resetMap = {
            all: () => { all.value = [0]; },
        };

        if (key && resetMap[key]) {
            // Reset only the specific key passed
            resetMap[key]();
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
        restoreWindow,

        startDrag,
        onDrag,
        stopDrag,
        setCursor,

        windowOutOfBounds,

        $reset,
    };
});


if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useActivitiesStore, import.meta.hot));
}
