export default {
    methods: {
        select(item, app = false) {
            if (item instanceof MouseEvent) {
                this.active = null;
                return;
            }

            this.active = item;

            if (this.active?.action) {
                this.active?.action();
            }

            if (app && !this.activities.find(activity => activity === item)) {
                this.activities.push(item);
            }
        },
    },
}
