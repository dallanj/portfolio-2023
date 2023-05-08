<template>
<section
    class="window-header h-10 rounded-t-xl flex items-center justify-center"
    :class="cursor"
    @mousedown="startDrag"
    @mouseup="stopDrag"
    @mouseleave="setCursor(false)">
    <h2 class="select-none">{{ application.label }}</h2>
</section>
</template>


<script lang="ts">
export default {
    props: {
        application: {
            type: Object,
            required: true,
        }
    },

    data() {
        return {
            applicationWindow: null,
            last: {
                x: 0,
                y: 0,
            },
            current: {
                x: 0,
                y: 0,
            },
            cursor: 'cursor-default',
        }
    },

    methods: {
        setCursor(move = false) {
            if (move) {
                this.cursor = 'cursor-move';
            } else {
                this.cursor = 'cursor-default';
            }
        },

        startDrag(event) {
            event.preventDefault();

            // Set the moveable cursor
            this.setCursor(true);

            // Current mouse coordinates
            this.current.x = event.clientX;
            this.current.y = event.clientY;

            // Application window element
            this.applicationWindow = document.getElementById(`${this.application.value}-application`);

            // Listen for mouse movement
            document.addEventListener('mousemove', this.drag);
            document.addEventListener('mouseup', this.stopDrag);
        },

        drag(event) {
            event.preventDefault();

            // Find difference between current and last mouse coordinates
            this.last.x = this.current.x - event.clientX;
            this.last.y = this.current.y - event.clientY;
            this.current.x = event.clientX;
            this.current.y = event.clientY;

            // Update the position of the application window with the new coordinates
            if (this.applicationWindow) {
                this.$emit('dragging-positions', {
                    x: this.applicationWindow.offsetLeft - this.last.x,
                    y: this.applicationWindow.offsetTop - this.last.y,
                });
            }
        },

        stopDrag() {
            this.setCursor();
            document.removeEventListener('mousemove', this.drag);
            document.removeEventListener('mouseup', this.stopDrag);
        }
    }
};
</script>
