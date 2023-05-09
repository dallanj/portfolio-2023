export default {
    methods: {
        select(item, app = false) {
            if (item instanceof MouseEvent) {
                this.activeTab = null;
                return;
            }

            if (!app) this.activeTab = item;

            // Call actionable tabs or applications
            if (this.active?.action) {
                this.active?.action();
            }

            // Open application windows
            if (app) {
                if (!this.activities.find(activity => activity === item)) {
                    if (item.application) this.activities.push(item);
                }

                this.setActiveWindow(this.activities.indexOf(item));
            }
        },

        setActiveWindow(index) {
            // Set application to be the last activity used (window will be at the top of UI)
            if (index !== -1) {
                this.activities.push(this.activities.splice(index, 1)[0]);
            }
        },
    },
}
