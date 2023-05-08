<template>
<article
    :id="`${application.value}-application`"
    class="resizeable fixed"
    :class="cursor"
    :style="{
        width: width + 'px',
        height: height + 'px',
        top: top + 'px',
        left: left + 'px'
    }"
    @mousedown="startResize"
    @mouseup="stopResize"
    @mousemove="setCursor">

    <ApplicationWindowHeader
        :application="application"
        @dragging-positions="draggingPositions" />
</article>
</template>
    
<script>
import ApplicationWindowHeader from './ApplicationWindowHeader.vue';

export default {
    components: {
        ApplicationWindowHeader,
    },

    props: {
        application: {
            type: Object,
            required: true,
        }
    },

    data() {
        return {
            startX: 0,
            startY: 0,
            startWidth: 0,
            startHeight: 0,
            startTop: 0,
            startLeft: 0,
            minWidth: 50,
            minHeight: 50,
            width: 200,
            height: 200,
            top: 100,
            left: 300,
            direction: null,
            cursor: 'cursor-default',
        }
    },

    methods: {
        setCursor(event) {
            if (!event.target.classList.contains('resizeable')) {
                this.cursor = 'cursor-default';
                return;
            }

            // Set the cursor based on the direction of resizing
            switch (this.findDirection(event)) {
                case 'left':
                case 'right':
                    this.cursor = 'cursor-ew-resize';
                    return;
                case 'top':
                case 'bottom':
                    this.cursor = 'cursor-ns-resize';
                    return;
                case 'right-top':
                case 'left-bottom':
                    this.cursor = 'cursor-nesw-resize';
                    return;
                case 'left-top':
                case 'right-bottom':
                    this.cursor = 'cursor-nwse-resize';
                return;
            }
        },

        startResize(event) {
            event.preventDefault();

            // Application window is only resizeable when the borders are held by mouse
            if (!event.target.classList.contains('resizeable')) return;

            // Current mouse coordinates
            this.startX = event.clientX;
            this.startY = event.clientY;

            // Starting size and positioning of the application window
            this.startWidth = this.width;
            this.startHeight = this.height;
            this.startTop = this.top;
            this.startLeft = this.left;

            // Which side(s) the element is being resized to
            this.direction = this.findDirection(event);

            // Listen for mouse movement
            document.addEventListener('mousemove', this.resize);
            document.addEventListener('mouseup', this.stopResize);
        },

        resize(event) {
            // Change in position of current and initial mouse position
            const deltaX = event.clientX - this.startX;
            const deltaY = event.clientY - this.startY;

            // Resize element based on direction
            switch (this.direction) {
                case 'left':
                    this.dragLeftTop(deltaX);
                    break;
                case 'right':
                    this.dragRightBottom(deltaX);
                    break;
                case 'top':
                    this.dragLeftTop(deltaY, false);
                    break;
                case 'bottom':
                    this.dragRightBottom(deltaY, false);
                    break;
                case 'left-top':
                    this.dragLeftTop(deltaX);
                    this.dragLeftTop(deltaY, false);
                    break;
                case 'right-top':
                    this.dragRightBottom(deltaX);
                    this.dragLeftTop(deltaY, false);
                    break;
                case 'left-bottom':
                    this.dragLeftTop(deltaX);
                    this.dragRightBottom(deltaY, false);
                    break;
                case 'right-bottom':
                    this.dragRightBottom(deltaX);
                    this.dragRightBottom(deltaY, false);
                    break;
            }
        },

        findDirection(event) {
            event.preventDefault();
            let direction = '';

            if (event.offsetX < 10) {
                direction = 'left';
            } else if (event.offsetX > this.width - 10) {
                direction = 'right';
            } else {
                direction = '';
            }
            
            if (event.offsetY < 10) {
                if (direction !== '') direction += '-';
                direction += 'top';
            } else if (event.offsetY > this.height - 10) {
                if (direction !== '') direction += '-';
                direction += 'bottom';
            }

            return direction;
        },

        dragRightBottom(mousePosChange, right = true) {
            const startSize = right ? this.startWidth : this.startHeight;
            const minSize = right ? this.minWidth : this.minHeight;
            
            if (right) {
                this.width = Math.max(startSize + mousePosChange, minSize);
            } else {
                this.height = Math.max(startSize + mousePosChange, minSize);
            }
        },

        dragLeftTop(mousePosChange, left = true) {
            const startSize = left ? this.startWidth : this.startHeight;
            const minSize = left ? this.minWidth : this.minHeight;
            const startPosition = left ? this.startLeft : this.startTop;
            const newSize = Math.max(startSize - mousePosChange, minSize);
            let position, size;

            if (newSize > minSize) {
                // If the new width is greater than minWidth, update both the width and the left position
                size = newSize;
                position = startPosition + mousePosChange;
            } else {
                // If the new width is equal to or less than minWidth, only update the width and adjust the left position to keep the left edge in place
                size = minSize;
                position = startPosition + (startSize - minSize);
            }

            // Update the position of the application window
            if (left) {
                this.left = position;
                this.width = size;
            } else {
                this.top = position;
                this.height = size;
            }
        },

        stopResize() {
            document.removeEventListener('mousemove', this.resize);
            document.removeEventListener('mouseup', this.stopResize);
        },

        draggingPositions(positions) {
            // Emitted positions from ApplicationWindowHeader when dragging
            this.left = positions.x;
            this.top = positions.y;
        },
    },
}
</script>
    