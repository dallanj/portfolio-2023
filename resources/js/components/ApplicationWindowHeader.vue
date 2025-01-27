<script setup>
// import { useDrag } from '@/composables/useDrag';
import { useActivitiesStore } from '@/stores/activities';
import { ref, reactive } from 'vue';

const model = defineModel({
    type: Object,
    required: true,
});

const {
    maximizeWindow,
} = useActivitiesStore();

// const {
//     startDrag,
//     stopDrag,
//     // setCursor
//     // removeActivity,
//     // maximizeWindow,
//     // minimizeWindow,
// } = useDrag();

// const applicationWindow = ref(null);
// const last = reactive({
//     x: 0,
//     y: 0,
// });
// const current = reactive({
//     x: 0,
//     y: 0,
// });
const cursor = ref('cursor-default');

const setCursor = (move = false) => {
    if (move) {
        cursor.value = 'cursor-move';
    } else {
        cursor.value = 'cursor-default';
    }
};

// const startDrag = (event) => {
//     // event.preventDefault();

//     // Set the moveable cursor
//     setCursor(true);

//     // Current mouse coordinates
//     current.x = event.clientX;
//     current.y = event.clientY;

//     console.log('startDrag', event.clientX, current.x);
//     // Application window element
//     applicationWindow.value = document.getElementById(`${model.value.data.value}-application`);

//     console.log(applicationWindow.value);
//     // Listen for mouse movement
//     document.addEventListener('mousemove', drag);
//     document.addEventListener('mouseup', stopDrag);
// };

// const drag = (event) => {
//     // event.preventDefault();

//     // Find difference between current and last mouse coordinates
//     last.x = current.x - event.clientX;
//     last.y = current.y - event.clientY;
//     current.x = event.clientX;
//     current.y = event.clientY;
//     console.log('drag', {
//         last: last.x,
//         current: current.x,
//     });

//     // Update the position of the application window with the new coordinates
//     if (applicationWindow.value) {
//         let newPos = {
//             x: applicationWindow.value.offsetLeft - last.x,
//             y: applicationWindow.value.offsetTop - last.y
//         };

//         console.log('drag', {
//             newPos: newPos.x,
//         });

//         // Top header and side navigation boundaries
//         if (newPos.y <= model.value.boundary.y) model.value.top = model.value.boundary.y;
//         if (newPos.x <= model.value.boundary.x) model.value.left = model.value.boundary.x;
        
//         model.value.top = newPos.y;
//         model.value.left = newPos.x;
//         // this.$emit('dragging-positions', newPos);
//     }
// };

// const stopDrag = () => {
//     setCursor();
//     document.removeEventListener('mousemove', drag);
//     document.removeEventListener('mouseup', stopDrag);
// };
import { useDrag } from '@/composables/useDrag.ts';
const containerRef = ref(null);

const onDrag = ({ delta, currentMousePosition, initialMousePosition, target }) => {
  if (target === 'canvas') {
    setCursor(true);

    const rect = containerRef.value.getBoundingClientRect();

    // The mouse needs to be in the some clientX/Y as it was originally
    const mousex = Math.min(
        Math.max(currentMousePosition.x - rect.left, 0),
        rect.width
    );
    const mousey = Math.min(
        Math.max(currentMousePosition.y - rect.top, 0),
        rect.height
    );

    // console.log('Test: ',mousex, currentMousePosition.x, rect.left)
 
    // console.log(`Dragging: ${target}`, {
    //     delta: delta.x,
    //     rect: rect.x,
    //     initialMousePosition: initialMousePosition.x,
    //     mousex: Math.max(
    //         Math.min(delta.x, initialMousePosition.x),
    //         rect.width - initialMousePosition.x
    //     ),
    //     currentMousePosition: currentMousePosition.x,
    //     // initalPosition: initialMousePosition.x,
    //     initialPositionX: Math.min(Math.max(currentMousePosition.x - rect.left, 0), rect.width),
    //     // mousex: mousex,
    //     mousex2: Math.min(initialMousePosition.x - rect.left, 0),
    //     target: Math.min(currentMousePosition.x - rect.left, 0),
    // });

    if (model.value.boundary.y < rect.y && currentMousePosition.y > 0) {
        model.value.top += delta.y
        
        model.value.outOfBounds.y = false;
        console.log('top', model.value.outOfBounds.y);
    } else if (mousey >= initialMousePosition.y) {
        model.value.top += delta.y
        model.value.outOfBounds.y = false;
        
    } else {
        model.value.outOfBounds.y = true;
        console.log('top', model.value.outOfBounds.y);
    }

    if (model.value.boundary.x < rect.x && currentMousePosition.x > 0) {
        model.value.left += delta.x
        
        model.value.outOfBounds.x = false;
        console.log('left', model.value.outOfBounds.x);
    } else if (mousex >= initialMousePosition.x) {
        model.value.left += delta.x
        model.value.outOfBounds.x = false;
        
    } else {
        model.value.outOfBounds.x = true;
        console.log('left', model.value.outOfBounds.x);
    }
  }
};

const onDrop = ({ delta, initalPosition, container, target }) => {
  console.log(`Dropped: ${target}`, {
    delta,
    container,
    target,
  });
  setCursor(false);

  if (model.value.outOfBounds.y) {
    maximizeWindow(model);
  }

  if (model.value.outOfBounds.x) {
    maximizeWindow(model, true);
  }
};

useDrag(onDrag, onDrop, { containerRef });
</script>

<template>
<section
    ref="containerRef"
    class="app-header h-10 flex items-center justify-center relative"
    :class="[
        cursor,
        { 'rounded-t-xl': model.roundedBorder }
    ]"
    data-draggable="canvas">
    <slot />
</section>
</template>