import { reactive, computed } from 'vue';

const state = reactive({
    all: [],
    dropdown: null,
});

/**
 * Add application to activities
 *
 * @param {Object} app
 */
const addActivity = app => {
    if (!findActivity(app) && app.application) {
        state.all.push(app);
    }
};

/**
 * Remove application remove activities
 *
 * @param {Object} app
 */
const removeActivity = app => {
    state.all.splice(state.all.indexOf(app), 1);
};

/**
 * Find application within activities
 *
 * @param {Object} app
 * 
 * @return {Object}
 */
const findActivity = app => {
    return state.all.find(activity => activity === app);
};


/**
 * Set the active window
 *
 * @param {Object} app
 */
const setActiveWindow = app => {
    state.all.push(state.all.splice(state.all.indexOf(app), 1)[0]);
}

/**
 * Set dropdown menu
 *
 * @param {Object} dropdown
 */
const setDropdown = dropdown => {
    state.dropdown = dropdown;
};

/**
 * Get the active window
 *
 * @return {Object}
 */
const getActiveWindow = computed(() => state.all.length > 0 ? state.all[state.all.length - 1] : null);

/**
 * Get the active dropdown
 *
 * @return {Object}
 */
const getDropdown = computed(() => state.dropdown);

export default {
    state,
    getActiveWindow,
    setActiveWindow,
    addActivity,
    removeActivity,
    setDropdown,
    getDropdown,
};
