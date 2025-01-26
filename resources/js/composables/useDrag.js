import { ref, reactive } from 'vue';

export function useDrag(activities) {
	const applicationWindow = ref(null);
	const last = reactive({
		x: 0,
		y: 0,
	});
	const current = reactive({
		x: 0,
		y: 0,
	});
	const cursor = ref('cursor-default');

	const setCursor = (activity, move = false) => {
		console.log(activity);
		if (move) {
			activity.cursor = 'cursor-move';
		} else {
			activity.cursor = 'cursor-default';
		}
	};

	const startDrag = (event, activity) => {
		event.preventDefault();
	
		// Set the moveable cursor
		// setCursor(activity.value, true);
	
		// Current mouse coordinates
		current.x = event.clientX;
		current.y = event.clientY;
	
		console.log('startDrag', activity.id, event.clientX, current.x);
		// Application window element
		applicationWindow.value = document.getElementById(`${activity.data.value}-application`);
	
		console.log(applicationWindow.value);
		// Listen for mouse movement
		document.addEventListener('mousemove', (e) => onDrag(e, activity));
		document.addEventListener('mouseup', (e) => stopDrag(e, activity));
	};
	
	const onDrag = (event, activity) => {
		event.preventDefault();
	
		// Find difference between current and last mouse coordinates
		last.x = current.x - event.clientX;
		last.y = current.y - event.clientY;
		current.x = event.clientX;
		current.y = event.clientY;
		console.log('drag', {
			last: last.x,
			current: current.x,
		});
	
		// Update the position of the application window with the new coordinates
		if (applicationWindow.value) {
			let newPos = {
				x: applicationWindow.value.offsetLeft - last.x,
				y: applicationWindow.value.offsetTop - last.y
			};
	
			console.log('drag', {
				newPos: newPos.x,
			});
	
			// Top header and side navigation boundaries
			if (newPos.y <= activity.boundary.y) activity.top = activity.boundary.y;
			if (newPos.x <= activity.boundary.x) activity.left = activity.boundary.x;
			
			activity.top = newPos.y;
			activity.left = newPos.x;
			// this.$emit('dragging-positions', newPos);
		}
	};
	
	const stopDrag = (activity) => {
		// Reset the cursor
		setCursor(activity, false);

		// Remove the event listeners
		document.removeEventListener('mousemove', onDrag);
		document.removeEventListener('mouseup', stopDrag);
	};

    return {
		startDrag,
		onDrag,
		stopDrag,
		setCursor,
    };
}
