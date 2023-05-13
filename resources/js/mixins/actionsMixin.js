export default {
    methods: {
        hasClickedOutside(item) {
            return item instanceof MouseEvent ? true : false;
        },
    },
}
