export default {
    methods: {
        select(item, app = false) {
            if (item instanceof MouseEvent) {
                this.active = null;
                return;
            }

            this.active = item;

            // Call actionable tabs or applications
            if (this.active?.action) {
                this.active?.action();
            }

            // Open application windows
            if (app && !this.activities.find(activity => activity === item)) {
                if (item.application) this.activities.push(item);
            }
        },
    },
}
