import { ref, computed } from 'vue';
import { useActivitiesStore } from '@/stores/activities';
import { useSettingsStore } from '@/stores/settings';

export function useResize(application) {
    const {
        boundaries,
        dockPosition
    } = useSettingsStore();
    
    const { setActiveWindow } = useActivitiesStore();

    // Reactive cursor changes to the direction(s) of resizing
    const cursor = ref('cursor-default');

    // Computed width, height for styling
    const windowWidth = computed(() => (
        Number.isInteger(application.value.width)
            ? `${application.value.width}px`
            : application.value.width
    ));

    const windowHeight = computed(() => (
        Number.isInteger(application.value.height)
            ? `${application.value.height}px`
            : application.value.height
    ));

    // Set active window and start resizing
    const startActions = (event) => {
        // Set application as the active window
        setActiveWindow(application?.value?.data);

        if (!event.target.classList.contains('app-window')) return;
        // Start resizing if target contains app-window class
        startResize(event);
    }

    // Change the cursor when hovering over the perimeter of the window
    const setCursor = (event) => {
        if (!event.target.classList.contains('app-window')) {
            cursor.value = 'cursor-default';
            return;
        }

        // Change cursor to the direction of resizing
        switch (findDirection(event)) {
            case 'left':
            case 'right':
                cursor.value = 'cursor-ew-resize';
                break;
            case 'top':
            case 'bottom':
                cursor.value = 'cursor-ns-resize';
                break;
            case 'right-top':
            case 'left-bottom':
                cursor.value = 'cursor-nesw-resize';
                break;
            case 'left-top':
            case 'right-bottom':
                cursor.value = 'cursor-nwse-resize';
                break;
        }
    }

    // Initial start of resizing the window
    const startResize = (event) => {
        event.preventDefault();
        if (!event.target.classList.contains('app-window')) return;

        // Apply initial dimensions and sizing to application
        application.value.startX = event.clientX;
        application.value.startY = event.clientY;
        application.value.startWidth = application.value.width;
        application.value.startHeight = application.value.height;
        application.value.startTop = application.value.top;
        application.value.startLeft = application.value.left;
        application.value.direction = findDirection(event);

        document.addEventListener('mousemove', resize);
        document.addEventListener('mouseup', stopResize);
    }

    // Resize the window logic
    const resize = (event) => {
        // Get the coords of the mouse event
        const deltaX = event.clientX - application.value.startX;
        const deltaY = event.clientY - application.value.startY;

        switch (application.value.direction) {
            case 'left':
                dragLeftTop(deltaX);
                break;
            case 'right':
                dragRightBottom(deltaX);
                break;
            case 'top':
                dragLeftTop(deltaY, false);
                break;
            case 'bottom':
                dragRightBottom(deltaY, false);
                break;
            case 'left-top':
                dragLeftTop(deltaX);
                dragLeftTop(deltaY, false);
                break;
            case 'right-top':
                dragRightBottom(deltaX);
                dragLeftTop(deltaY, false);
                break;
            case 'left-bottom':
                dragLeftTop(deltaX);
                dragRightBottom(deltaY, false);
                break;
            case 'right-bottom':
                dragRightBottom(deltaX);
                dragRightBottom(deltaY, false);
                break;
            }
    }

    // Get the direction(s) of the mouse on the perimeter of the window
    const findDirection = (event) => {
        let dir = '';

        if (event.offsetX < 10) {
            dir = 'left';
        } else if (event.offsetX > application.value.width - 10) {
            dir = 'right';
        }

        if (event.offsetY < 10) {
            dir += dir ? '-top' : 'top';
        } else if (event.offsetY > application.value.height - 10) {
            dir += dir ? '-bottom' : 'bottom';
        }

        return dir;
    }

    const dragRightBottom = (mousePosChange, isWidth = true) => {
        if (isWidth) {
            application.value.width = Math.max(application.value.startWidth + mousePosChange, application.value.minWidth);
            application.value.previousWidth = application.value.width;
        } else {
            application.value.height = Math.max(application.value.startHeight + mousePosChange, application.value.minHeight);
            application.value.previousHeight = application.value.height;
        }
    }

    const dragLeftTop = (mousePosChange, isLeft = true) => {
        const startSize = isLeft ? application.value.startWidth : application.value.startHeight;
        const minSize = isLeft ? application.value.minWidth : application.value.minHeight;
        const startPosition = isLeft ? application.value.startLeft : application.value.startTop;

        const newSize = Math.max(startSize - mousePosChange, minSize);

        if (isLeft) {
            // Return if new size is the minimum width
            if (newSize <= application.value.minWidth) return;
            application.value.left = Math.max(startPosition + mousePosChange, boundaries.left);
            application.value.width = newSize;
            application.value.previousLeft = application.value.left;
            application.value.previousWidth = application.value.width;
        } else {
            // Return if new size is the minimum height
            if (newSize <= application.value.minHeight) return;
            application.value.top = Math.max(startPosition + mousePosChange, boundaries.top);
            application.value.height = newSize;
            application.value.previousTop = application.value.top;
            application.value.previousHeight = application.value.height;
        }
    }

    const stopResize = () => {
        document.removeEventListener('mousemove', resize);
        document.removeEventListener('mouseup', stopResize);
    }

    return {
        cursor,
        windowWidth,
        windowHeight,
        startActions,
        setCursor,
    };
}
