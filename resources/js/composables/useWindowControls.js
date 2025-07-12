import { useSettingsStore } from "@/stores/settings";
import { useActivitiesStore } from "@/stores/activities";
import { nextTick, computed, inject } from 'vue';
import { getBoundingRectFromModel, getAppIdFromModel } from '@/utils/helpers';
import { storeToRefs } from "pinia";

export function useWindowControls(activities) {
	const { isMobile } = inject('screenSize');
	const { boundaries, dockPosition, snapThresholds } =
        useSettingsStore();

	const {
		getActiveWindow,
	} = storeToRefs(useActivitiesStore());
	
	const activeOutOfBounds = computed(() => getActiveWindow?.value?.outOfBounds || {});
		
	/**
     * Minimize the window
     *
     * @param {Ref<Object>} activity - The activity ref object to be minimized.
     */
	const minimizeWindow = (activity) => {
		if (!activity?.value) return;

		const appId = getAppIdFromModel(activity.value);
		const dockEl = document.getElementById(`nav-item-${appId}`);

		if (!dockEl) {
			console.debug('Missing elements for animation');
			activity.value.minimized = true;
			return;
		}

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
	};

	/**
	 * Stores the previous coordinates before maximizing a window.
	 *
	 * @param {Ref<Object>} activity - The activity ref object to be maximized.
	 * @returns {Ref<Object>} An object with the window's current position and size: 
	 */
	const storePreviousWindowPosition = (activity) => {
		const appRect = getBoundingRectFromModel(activity.value);

		// Make sure previous positions are not out of bounds
		if (appRect.top < snapThresholds.top) {
			activity.value.previousTop = snapThresholds.top;
		} else {
			activity.value.previousTop = appRect.top;
		}

		if (appRect.left < snapThresholds.left) {
			activity.value.previousLeft = snapThresholds.left;
		} else {
			activity.value.previousLeft = appRect.left;
		}

		if (appRect.right < snapThresholds.right) {
			activity.value.previousRight = snapThresholds.right;
		} else {
			activity.value.previousRight = appRect.reft;
		}

		activity.value.previousHeight = appRect.height;
		activity.value.previousWidth = appRect.width;
		
		return activity.value;
	};

	/**
	 * Maximizes the window.
	 *
	 * @param {Ref<Object>} activity - The activity ref object to be maximized.
	 * @param {boolean} [halfScreen=false] - Whether to maximize to half the screen width.
	 * @param {boolean} [dockChange=false] - If true, indicates the maximize is due to a dock position change.
	 */
	const maximizeWindow = (activity, halfScreen = false, dockChange = false) => {
		activity.value.outOfBounds = {
			left: false,
			right: false,
			top: false,
			x: false,
			y: false
		};
	
		if (!halfScreen) {
			storePreviousWindowPosition(activity);
		}

		// Skip full unmaximize logic if already maximized and not half-screen
		if (!dockChange && activity.value.maximized && !activity.value.halfScreen) {
			unMaximizeWindow(activity);
			return;
		}
	
		activity.value.maximizing = true;
	
		setTimeout(() => {
			const isHalfScreen = halfScreen;
			const isRightSide = isHalfScreen && activity.value.outOfBounds.right;
			const isLeftSide = isHalfScreen && activity.value.outOfBounds.left;
	
			activity.value.roundedBorder = false;
			activity.value.top = boundaries.top;
	
			// Set left position for left or right half
			if (isRightSide) {
				console.log('isRightSide',isRightSide);
				activity.value.left = `calc(50% + ${boundaries.left / 2}px)`;
			} else {
				console.log('isLeftSide',isLeftSide);
				activity.value.left = boundaries.left;
			}
	
			activity.value.width = isHalfScreen
				? `calc(50% - ${boundaries.left}px)`
				: `calc(100% - ${boundaries.left}px)`;
	
			activity.value.height = `calc(100% - ${boundaries.top + boundaries.bottom}px)`;
	
			activity.value.maximized = true;
			activity.value.halfScreen = isHalfScreen;
			activity.value.maximizing = false;
		}, dockChange ? 0 : 500);
	};

	/**
	 * Un-maximizes the window.
	 *
	 * @param {Ref<Object>} activity - The activity ref object to be un-maximized.
	 * @param {Object|null} [coords=null] - Optional coordinates to restore the window to (top, left, width, height).
	 */
	const unMaximizeWindow = (activity, coords = null) => {
		// const activity = unref(activityRef);

		// // Get the app's bounding rectangle data
		// const appRect = getBoundingRectFromModel(activity.value);

		// activity.value.previousTop = appRect.top;
		// activity.value.previousLeft = appRect.left;
		// activity.value.previousHeight = appRect.height;
		// activity.value.previousWidth = appRect.width;

		// console.info('unMaximizeWindow', appRect, activity);
		// Mark the activity as being removed
		activity.value.maximizing = true;
		activity.value.roundedBorder = true;
		
  
		// Wait for the animation to complete before actually removing the file from the list
		setTimeout(() => {
			if (coords) {
				activity.value.top = coords.y;
				activity.value.left = coords.x;
				console.log('coords', {
					coords
				})
			} else {
				console.log('unMaximizeWindow', {
					top: activity.value.previousTop,
					left: activity.value.previousLeft
				})
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
