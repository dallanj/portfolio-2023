import { nextTick, inject, ref } from 'vue';
import { useWindowControls } from '../composables/useWindowControls';
import { useSettingsStore } from '../stores/settings';

export function useActivityControls(activities) {
	const { isMobile } = inject('screenSize');
	const { maximizeWindow } = useWindowControls();
	const { boundaries, dockPosition } = useSettingsStore();

    const removeActivity = (activity) => {
		// Mark the activity as being removed
		activity.value.closing = true;

		// Find the specific window we are closing
		const appId = activity.value.id.replace('-activity', '');
		const windowEl = document.getElementById(`${appId}-application`);
		if (!windowEl) return;
		// Trigger CSS class for zoom out
		if (windowEl) {
			windowEl.classList.add('zoom-out');
		}
  
		// Wait for the animation to complete before actually removing the file from the list
		setTimeout(() => {
			const index = activities.value.indexOf(activity.value);
			if (index > -1) {
				activities.value.splice(index, 1);
			}
	
			// Move the next visible (non-minimized) window to the end
			const next = activities.value.find(a => !a.minimized && !a.closing);

			// Trying to find the next activity/window if there is one available
			if (next) {
				// Reorder it to the end (top of stack)
				const nextIndex = activities.value.indexOf(next);
				if (nextIndex > -1) {
					activities.value.splice(nextIndex, 1);
					activities.value.push(next);
				}
			}
		}, 200);
	};

	const removeAllActivities = _ => {
		activities.value.forEach((activity) => {
			activity.closing = true;
		});
		setTimeout(() => {
			activities.value = [];
		}, 500);
	};

	/**
     * Add a new activity or re-open an existing instance
     * @param {Object} activity - The application object containing a `value` key.
     */
	const addActivity = async (activity) => {
		// Create a new activity using the constructor
		const newActivity = new Activity(activity);
	
		if (!activityExists(newActivity.id) && newActivity.data.application) {
			// Add the app window to array of activities
			activities.value.push(newActivity);

			// Wait for DOM to mount
			await nextTick();

			// Find the new activity by ID and add a zoom in animation
			setTimeout(() => {
				const windowEl = document.getElementById(`${newActivity.data.value}-application`);
				windowEl.classList.add('zoom-in');
				// Remove the zoom in animation once finished
				windowEl.addEventListener('animationend', () => {
					windowEl.classList.remove('zoom-in');
				}, { once: true });
			}, 200);

			// Delay clearing `starting` on the activity to allow transition setup to take effect
			setTimeout(async () => {
				
				// Find the index within activities of the new activity
				const index = activities.value.findIndex(a => a.id === newActivity.id);
				if (index !== -1) {
					// Update the object by creating a new one (spread to keep reactivity clean)
					activities.value[index] = { ...activities.value[index], starting: false };
				}
				// Let DOM update again
				await nextTick();
			}, 250);

		} else if (activityExists(newActivity.id) && newActivity.data.application) {
			// If the activity exists, find the it within activities array
			const existing = activities.value.find(a => a.id === newActivity.id);
			if (!existing) return;
	
			// Get the dock element ID and replace the exisiting activity id without '-activity'
			const appId = existing.id.replace('-activity', '');
			const dockEl = document.getElementById(`nav-item-${appId}`);
	
			// Show the window first (so it's visible and has layout)
			existing.minimized = false;
			await nextTick();
	
			// Find the activity by ID for animation otherwise, return
			const windowEl = document.getElementById(`${appId}-application`);
			if (!dockEl || !windowEl) return;
	
			// Calculate transform offset
			const dockRect = dockEl.getBoundingClientRect();
			const dockCenterX = dockRect.left + dockRect.width / 2 + window.scrollX;
			const dockCenterY = dockRect.top + dockRect.height / 2 + window.scrollY;
	
			const windowX = existing.left;
			const windowY = existing.top;
			const windowWidth = parseFloat(getComputedStyle(windowEl).width);
			const windowHeight = parseFloat(getComputedStyle(windowEl).height);
	
			const winCenterX = windowX + windowWidth / 2;
			const winCenterY = windowY + windowHeight / 2;
	
			const deltaX = dockCenterX - winCenterX;
			const deltaY = dockCenterY - winCenterY;
	
			// Step 1: Set initial transform at full size but dock position
			// Set transform first
			existing.transform = `translate(${deltaX}px, ${deltaY}px) scale(0.1)`;

			// Ensure it's applied
			await nextTick();

			// Make it visible
			existing.minimized = false;

			await nextTick();

			// Get the DOM element again in case re-render changed it
			const el = document.getElementById(`${appId}-application`);
			if (!el) return;

			// Force layout flush (reflow)
			el.offsetHeight;

			// Now animate
			existing.transform = '';
	
			// Step 2: Let browser render initial state
			requestAnimationFrame(() => {
				// Step 3: Animate to normal position
				requestAnimationFrame(() => {
					existing.transform = ''; // Triggers animation
				});
			});
	
			// Step4: Re-order after animation
			setTimeout(() => {
				const index = activities.value.findIndex(a => a.id === existing.id);
				if (index > -1) {
					activities.value.push(activities.value.splice(index, 1)[0]);
				}
			}, 400);
		}
	};

	const addActivities = (activities) => {
		let newActivies = [...activities]
		  	.map((activity) => new Activity(activity))
		  	.filter((activity) => !activityExists(activity.id));
		activities.value = activities.value.concat(newActivies);
	};

	const activityExists = (otherId) => {
		return activities.value.some(({ id }) => id === otherId);
	};

	class Activity {
		constructor(activity) {
			this.data = activity;
			this.id = `${activity.value}-activity`;
			this.minimized = false;
			this.maximized = false;
			this.halfScreen = false;
			this.minimizing = false;
			this.maximizing = false;
			this.closing = false;

			this.startX = activity.left;
			this.startY = activity.top;
			this.startWidth = activity.width;
            this.startHeight = activity.height;
            this.startTop = this.startY;
            this.startLeft = this.startX;

            this.width = this.startWidth;
            this.height = this.startHeight;
            this.top = this.startTop;
            this.left = this.startLeft;

			this.minWidth = 300
			this.minHeight = 300;

			this.previousWidth = this.width;
			this.previousHeight = this.height;
			this.previousTop = this.top;
			this.previousLeft = this.left;

			this.roundedBorder = true;

            this.direction = null;
            this.cursor = 'cursor-default';
            // this.boundary = { x: 80, y: 32 };
			this.outOfBounds = { left: false, right: false, top: false, x: false, y: false };
			this.transform = '';
			this.starting = true;
		}
	};

    return {
		addActivity,
		addActivities,
		removeAllActivities,
        removeActivity,
		activityExists,
    };
}
