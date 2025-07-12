/**
 * Get the current bounding rectangle details of an application window in the DOM.
 *
 * @param {Object} model - The activity model object containing an `id` key (e.g., 'terminal-activity').
 * @returns {Object|null} An object with the window's current position and size: 
 * { left, top, right, bottom, width, height }, or null if the DOM element is not found.
 *
 * Example return:
 * {
 *   left: 100,
 *   top: 50,
 *   right: 500,
 *   bottom: 300,
 *   width: 400,
 *   height: 250
 * }
 *
 * Notes:
 * - The function uses `document.getElementById()` based on the model's `id`.
 * - It assumes your application window DOM element follows the ID format: `${appId}-application`.
 * - Returns null if the DOM element cannot be found.
 */
export function getBoundingRectFromModel(model) {
    const appId = getAppIdFromModel(model);
    const appEl = document.getElementById(`${appId}-application`);

    if (!appEl) {
        console.warn(`App element for '${appId}' not found in DOM.`);
        return null;
    }

    const rect = appEl.getBoundingClientRect();

    return {
        left: rect.left,
        top: rect.top,
        right: rect.right,
        bottom: rect.bottom,
        width: rect.width,
        height: rect.height,
    };
};

/**
 * Removes `-activity` from activity models id key
 * 
 * @param {Object} model - The activity model object containing an `id` key (e.g., 'terminal-activity').
 * @returns {String|null} A string with only the activity model name 
 */
export function getAppIdFromModel(model) {
    if (!model?.id) return null;
    return model.id.replace('-activity', '');
};