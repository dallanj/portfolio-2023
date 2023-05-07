<template>
<MainLayout>
    <div
        class="resizeable absolute bg-white p-2"
        :class="cursor"
        :style="{width: width + 'px', height: height + 'px', top: top + 'px', left: left + 'px'}"
        @mousedown="startResize"
        @mouseup="stopResize"
        @mousemove="changeCursor">
        <p class="w-full h-full bg-black">Draggable Window Test</p>
    </div>
</MainLayout>
</template>

<script>
export default {
    data() {
        return {
            isResizing: false,
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
            top: 0,
            left: 0,
            direction: null,
            cursor: 'cursor-default',
        }
    },

    methods: {
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

        changeCursor(event) {
            if (!event.target.classList.contains('resizeable')) {
                this.cursor = 'cursor-default';
                return;
            }

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
            if (!event.target.classList.contains('resizeable')) return;
            event.preventDefault();
            this.isResizing = true;
            this.startX = event.clientX;
            this.startY = event.clientY;
            this.startWidth = this.width;
            this.startHeight = this.height;
            this.startTop = this.top;
            this.startLeft = this.left;
            this.direction = this.findDirection(event);
            document.addEventListener('mousemove', this.resize);
            document.addEventListener('mouseup', this.stopResize);
        },

        resize(event) {
            if (!this.isResizing) return;
            const deltaX = event.clientX - this.startX;
            const deltaY = event.clientY - this.startY;
            switch (this.direction) {
                case 'left':
                    this.width = Math.max(this.startWidth - deltaX, this.minWidth);
                    this.left = this.startLeft + deltaX;
                    break;
                case 'right':
                    this.width = Math.max(this.startWidth + deltaX, this.minWidth);
                    break;
                case 'top':
                    this.height = Math.max(this.startHeight - deltaY, this.minHeight);
                    this.top = this.startTop + deltaY;
                    break;
                case 'bottom':
                    this.height = Math.max(this.startHeight + deltaY, this.minHeight);
                    break;
                case 'left-top':
                    this.width = Math.max(this.startWidth - deltaX, this.minWidth);
                    this.left = this.startLeft + deltaX;
                    this.height = Math.max(this.startHeight - deltaY, this.minHeight);
                    this.top = this.startTop + deltaY;
                    break;
                case 'right-top':
                    this.width = Math.max(this.startWidth + deltaX, this.minWidth);
                    this.height = Math.max(this.startHeight - deltaY, this.minHeight);
                    this.top = this.startTop + deltaY;
                    break;
                case 'left-bottom':
                    this.width = Math.max(this.startWidth - deltaX, this.minWidth);
                    this.left = this.startLeft + deltaX;
                    this.height = Math.max(this.startHeight + deltaY, this.minHeight);
                    break;
                case 'right-bottom':
                    this.width = Math.max(this.startWidth + deltaX, this.minWidth);
                    this.right = this.startRight - deltaX;
                    this.height = Math.max(this.startHeight + deltaY, this.minHeight);
                    break;
            }
        },

        stopResize() {
            this.isResizing = false;
            document.removeEventListener('mousemove', this.resize);
            document.removeEventListener('mouseup', this.stopResize);
        }
    },
}
</script>
