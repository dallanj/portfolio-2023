export function useWindowControls(activities) {
	const minimizeWindow = (activity) => {
		console.log('minimize', activity.value);
		// Mark the activity as being removed
		activity.value.minimizing = true;
  
		// Wait for the animation to complete before actually removing the file from the list
		setTimeout(() => {
			const index = activities.value.indexOf(activity.value);
			if (index > -1) activities.value.splice(index, 1);
		}, 500);
		// toggleApplicationVisibility(application.value, false);
		// activitiesStore.removeActiveWindow(application.value);
	}

	const maximizeWindow = (activity, halfScreen = false) => {
		console.log('maximize', activity.value);
		activity.value.outOfBounds = {
			x: false,
			y: false,
		}

		// Mark the activity as being removed
		if (activity.value.maximized && !activity.value.halfScreen) {
			unMaximizeWindow(activity);
		} else {
			if (!halfScreen && !activity.value.halfScreen) {
				activity.value.previousTop = activity.value.top;
				activity.value.previousLeft = activity.value.left;
				activity.value.previousWidth = activity.value.width;
				activity.value.previousHeight = activity.value.height;
			}
			
			activity.value.maximizing = true;
  
			// Wait for the animation to complete before actually removing the file from the list
			setTimeout(() => {
				activity.value.roundedBorder = false;
				activity.value.top = activity.value.boundary.y;
				activity.value.left = activity.value.boundary.x;
				activity.value.width = `calc(${halfScreen ? '50' : '100'}% - ${activity.value.boundary.x}px)`;
				activity.value.height = '100%';
				activity.value.maximized = true;
				activity.value.halfScreen = halfScreen;
				activity.value.maximizing = false;
			}, 500);
		}	
	}

	const unMaximizeWindow = (activity) => {
		console.log('un-maximize', activity.value);
		// Mark the activity as being removed
		activity.value.maximizing = true;
		activity.value.roundedBorder = true;
		
  
		// Wait for the animation to complete before actually removing the file from the list
		setTimeout(() => {
			activity.value.top = activity.value.previousTop;
			activity.value.left = activity.value.previousLeft;
			activity.value.width = activity.value.previousWidth;
			activity.value.height = activity.value.previousHeight;
			activity.value.halfScreen = false;
			activity.value.maximized = false;
			activity.value.maximizing = false;
		}, 500);	
	}

    return {
		minimizeWindow,
		maximizeWindow,
    };
}
