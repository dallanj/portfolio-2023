export default {
    methods: {
        hasClickedOutside(item) {
            return item instanceof MouseEvent ? true : false;
        },

        toggleApplicationVisibility(app, visible = true) {
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
        },

        isApplicationVisible(app) {
            const applicationElement = document.getElementById(`${app.value}-application`);

            if (!applicationElement) {
                return false;
            }

            return applicationElement.classList.contains('block');
        },
    },
}
