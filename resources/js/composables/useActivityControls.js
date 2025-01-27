export function useActivityControls(activities) {
    const removeActivity = (activity) => {
        console.log('RemoveActivity', activity.value.id);
		// Mark the activity as being removed
		activity.value.closing = true;
  
		// Wait for the animation to complete before actually removing the file from the list
		setTimeout(() => {
			const index = activities.value.indexOf(activity.value);
			if (index > -1) activities.value.splice(index, 1);
		}, 500);
	};

	const removeAllActivities = _ => {
		activities.value.forEach((activity) => {
			activity.closing = true;
		});
		setTimeout(() => {
			activities.value = [];
		}, 500);
	};

	const addActivity = (activity) => {
        const newActivity = new Activity(activity)

        if (!activityExists(newActivity.id) && newActivity.data.application) {
            activities.value.push(newActivity);
            // saveToLocalStorage();
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

			this.startX = 100;
			this.startY = 75;
			this.startWidth = 200;
            this.startHeight = 200;
            this.startTop = 0;
            this.startLeft = 0;

            this.width = 300;
            this.height = 300;
            this.top = 100;
            this.left = 100;

			this.minWidth = 300
			this.minHeight = 300;

			this.previousWidth = this.width;
			this.previousHeight = this.height;
			this.previousTop = this.top;
			this.previousLeft = this.left;

			this.roundedBorder = true;

            this.direction = null;
            this.cursor = 'cursor-default';
            this.boundary = { x: 80, y: 32 };
			this.outOfBounds = { x: false, y: false }
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
