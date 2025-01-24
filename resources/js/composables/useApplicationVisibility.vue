<script>
export function useApplicationVisibility() {
    /**
     * Check if a click event occurred outside a specific item.
     * @param {MouseEvent|Object} item - The item to check.
     * @returns {boolean} - True if it's a MouseEvent, otherwise false.
     */
    function hasClickedOutside(item) {
        return item instanceof MouseEvent;
    }

    /**
     * Toggle the visibility of an application element.
     * @param {Object} app - The application object containing a `value` key.
     * @param {boolean} visible - Whether to show or hide the application.
     */
    function toggleApplicationVisibility(app, visible = true) {
        const applicationElement = document.getElementById(`${app.value}-application`);

        if (!applicationElement) {
            return;
        }

        if (visible) {
            applicationElement.classList.remove('hidden');
            applicationElement.classList.add('block');
        } else {
            applicationElement.classList.remove('block');
            applicationElement.classList.add('hidden');
        }
    }

    /**
     * Check if an application element is visible.
     * @param {Object} app - The application object containing a `value` key.
     * @returns {boolean} - True if the application element is visible, otherwise false.
     */
    function isApplicationVisible(app) {
        const applicationElement = document.getElementById(`${app.value}-application`);

        if (!applicationElement) {
            return false;
        }

        return applicationElement.classList.contains('block');
    }

    return {
        hasClickedOutside,
        toggleApplicationVisibility,
        isApplicationVisible,
    };
}
</script>