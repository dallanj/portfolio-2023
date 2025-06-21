import { useSettingsStore } from "@/stores/settings";
import { nextTick } from 'vue';

export function useWindowControls(activities) {

	const { boundaries, dockPosition } =
        useSettingsStore();

	/**
     * Minimize the window
     *
     * @param {Ref<Object>} activity - The activity ref object to be minimized.
     */
	const minimizeWindow = (activity) => {
		if (!activity?.value) return;

		const activityId = activity.value.id;
		const appId = activityId.replace('-activity', '');
		const dockEl = document.getElementById(`nav-item-${appId}`);

		if (!appId || !dockEl) {
			console.debug('Missing elements for animation');
			activity.value.minimized = true;
			return;
		}
		if (!dockEl) return;

		// Get dock center
		const dockRect = dockEl.getBoundingClientRect();
		const dockCenterX = dockRect.left + dockRect.width / 2 + window.scrollX;
		const dockCenterY = dockRect.top + dockRect.height / 2 + window.scrollY;

		// Use known top/left + size to calculate window center
		const windowX = activity.value.left;
		const windowY = activity.value.top;
		const windowWidth = parseFloat(getComputedStyle(document.getElementById(`${appId}-application`)).width);
		const windowHeight = parseFloat(getComputedStyle(document.getElementById(`${appId}-application`)).height);

		const winCenterX = windowX + windowWidth / 2;
		const winCenterY = windowY + windowHeight / 2;

		// Offset needed to animate from window to dock
		const deltaX = dockCenterX - winCenterX;
		const deltaY = dockCenterY - winCenterY;

		activity.value.transform = `translate(${deltaX}px, ${deltaY}px) scale(0.1)`;
		activity.value.minimizing = true;

		setTimeout(() => {
			activity.value.minimized = true;
			activity.value.minimizing = false;
			activity.value.transform = '';
		}, 400);
	}

	/**
	 * Maximizes the window.
	 *
	 * @param {Ref<Object>} activity - The activity ref object to be maximized.
	 * @param {boolean} [halfScreen=false] - Whether to maximize to half the screen width.
	 * @param {boolean} [dockChange=false] - If true, indicates the maximize is due to a dock position change.
	 */
	const maximizeWindow = (activity, halfScreen = false, dockChange = false) => {
		activity.value.outOfBounds = {
			x: false,
			y: false,
		}

		// If we're resizing due to a dock change, skip unmaximize logic
		if (!dockChange && activity.value.maximized && !activity.value.halfScreen) {
			unMaximizeWindow(activity);
			return;
		}

		// Store previous position and size if it's a fresh maximize (not from dock change)
		if (!dockChange && !halfScreen && !activity.value.halfScreen) {
			activity.value.previousTop = activity.value.top;
			activity.value.previousLeft = activity.value.left;
			activity.value.previousWidth = activity.value.width;
			activity.value.previousHeight = activity.value.height;
		}

		activity.value.maximizing = true;

		setTimeout(() => {
			activity.value.roundedBorder = false;
			activity.value.top = boundaries.top;
			activity.value.left = boundaries.left;
			
			// Respect boundaries of the dock and if the window is half screen
			if (dockPosition === 'left') {
				activity.value.width = `calc(${halfScreen ? '50' : '100'}% - ${boundaries.left}px)`;
				activity.value.height = `calc(100% - ${boundaries.top + boundaries.bottom}px)`;
			} else {
				activity.value.height = `calc(100% - ${boundaries.top + boundaries.bottom}px)`;
				activity.value.width = `calc(${halfScreen ? '50' : '100'}% - ${boundaries.left}px)`;
			}

			activity.value.maximized = true;
			activity.value.halfScreen = halfScreen;
			activity.value.maximizing = false;
		}, dockChange ? 0 : 500); // ⚡ Skip animation delay	
	};

	/**
	 * Un-maximizes the window.
	 *
	 * @param {Ref<Object>} activity - The activity ref object to be un-maximized.
	 * @param {Object|null} [coords=null] - Optional coordinates to restore the window to (top, left, width, height).
	 */
	const unMaximizeWindow = (activity, coords = null) => {
		// Mark the activity as being removed
		activity.value.maximizing = true;
		activity.value.roundedBorder = true;
		
  
		// Wait for the animation to complete before actually removing the file from the list
		setTimeout(() => {
			if (coords) {
				activity.value.top = coords.y;
				activity.value.left = coords.x;
			} else {
				activity.value.top = activity.value.previousTop;
				activity.value.left = activity.value.previousLeft;
			}
			
			activity.value.width = activity.value.previousWidth;
			activity.value.height = activity.value.previousHeight;
			activity.value.halfScreen = false;
			activity.value.maximized = false;
			activity.value.maximizing = false;
		}, 500);	
	}

	/**
     * Restore minimized windows with animation
     *
     * @param {Ref<Object>} activity - The activity ref object to be restored from being minimized with animation.
     */
	const restoreWindow = async (activity) => {
		if (!activity?.id) return;
	
		const activityId = activity.id;
		const appId = activityId.replace('-activity', '');
		const dockEl = document.getElementById(`nav-item-${appId}`);
	
		if (!dockEl) {
			console.warn('Missing dock or window element');
			return;
		}

		// 4. Calculate transition animation from dock → window
		const dockRect = dockEl.getBoundingClientRect();
		const dockCenterX = dockRect.left + dockRect.width / 2 + window.scrollX;
		const dockCenterY = dockRect.top + dockRect.height / 2 + window.scrollY;

		const windowX = activity.left;
		const windowY = activity.top;
		const windowWidth = activity.width || 500; // default fallback
		const windowHeight = activity.height || 500;

		const winCenterX = windowX + windowWidth / 2;
		const winCenterY = windowY + windowHeight / 2;

		const deltaX = dockCenterX - winCenterX;
		const deltaY = dockCenterY - winCenterY;

		// Step 1: Set initial transform before showing
		activity.transform = `translate(${deltaX}px, ${deltaY}px) scale(0.1)`;

		// Step 2: Show the window
		activity.minimized = false;

		// Step 3: Wait for DOM to render with transform
		await nextTick();

		const windowEl = document.getElementById(`${appId}-application`);
		if (!windowEl) {
			console.warn('Window element still not found');
			return;
		}

		// Step 4: Force layout flush
		windowEl.offsetHeight;

		// Step 5: Animate to normal position
		requestAnimationFrame(() => {
			activity.transform = '';
		});

		// Step 6: Bring to front
		setTimeout(() => {
			const index = activities.value.findIndex(a => a.id === activity.id);
			if (index > -1) {
				activities.value.push(activities.value.splice(index, 1)[0]);
			}
		}, 400);
	};

    return {
		minimizeWindow,
		maximizeWindow,
		unMaximizeWindow,
		restoreWindow,
    };
}
