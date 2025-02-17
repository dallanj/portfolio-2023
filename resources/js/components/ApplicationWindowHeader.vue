<script setup>
import { ref } from 'vue';
import { useActivitiesStore } from '@/stores/activities';
import { useDrag } from '@/composables/useDrag.ts';
import { useSettingsStore } from '@/stores/settings';
import { storeToRefs } from 'pinia';

const {
    boundaries,
    dockPosition
} = useSettingsStore();

const model = defineModel({
    type: Object,
    required: true,
});

const { maximizeWindow, unMaximizeWindow } = useActivitiesStore();

const cursor = ref('cursor-default');
const containerRef = ref(null);

const setCursor = (move = false) => {
    cursor.value = move
        ? 'cursor-move'
        : 'cursor-default';
};

// const startDrag = ({ target }) => {
    // if (target === 'header') {
    //     if (model.value.maximized || model.value.halfScreen) {
    //         model.value.maximized = true;
    //         model.value.halfScreen = false;
    //         maximize();
    //     }
    // }
// };

const onDrag = ({ delta, currentMousePosition, initialMousePosition, target }) => {
    if (target === 'header') {
        console.debug('initialMousePosition', initialMousePosition)
        // if (model.value.maximized || model.value.halfScreen) {
        //     model.value.maximized = true;
        //     model.value.halfScreen = false;
        //     unMaximizeWindow(model, currentMousePosition);

        //     // model.value.left = currentMousePosition.x;
        //     // model.value.right = currentMousePosition.y;
        //     // console.debug(currentMousePosition)

            
        //     return;
        // }

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

        if (model.value.maximized || model.value.halfScreen) {
            setTimeout(() => {
                isDragging.value = false;
            }, 1000);
            model.value.maximized = true;
            model.value.halfScreen = false;
            unMaximizeWindow(model, {
                x: currentMousePosition.x - (model.value.previousWidth / 2), //Math.max((currentMousePosition.x + delta.x + model.value.previousLeft) / 2, 0),
                y: currentMousePosition.y - (rect.height / 2), //Math.max((currentMousePosition.y + delta.y + model.value.previousTop) / 2, 0),
            });

            // model.value.left = currentMousePosition.x;
            // model.value.right = currentMousePosition.y;
            console.debug('ondrag',initialMousePosition,model.value.previousWidth, delta)

            
            // return;
                
        } 
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

            if (boundaries.top < rect.y && currentMousePosition.y > 0) {
                // isDragging.value = true;
                model.value.top += delta.y
                
                model.value.outOfBounds.y = false;
                console.log('top', model.value.outOfBounds.y);
            } else if (mousey >= initialMousePosition.y) {
                // isDragging.value = true;
                console.debug('mousey', mousey, initialMousePosition.y);
                model.value.top += delta.y
                model.value.outOfBounds.y = false;
                
            } else {
                // isDragging.value = false;
                model.value.outOfBounds.y = true;
                console.log('top', model.value.outOfBounds.y);
            }

            if (boundaries.left < rect.x && currentMousePosition.x > 80) {
                
                // isDragging.value = true;
                
                model.value.left += delta.x
                
                model.value.outOfBounds.x = false;
                console.log('left', model.value.outOfBounds.x);
            } else if (mousex >= initialMousePosition.x && currentMousePosition.x > 80) {
                // isDragging.value = true;
                // model.value.left = Math.max(model.value.left + delta.x , 80);
                model.value.left += delta.x
                model.value.outOfBounds.x = false;
                console.debug('mousex', mousex, currentMousePosition.x, initialMousePosition.x);
                
            } else {
                // isDragging.value = false;
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
        maximize();
    }

    if (model.value.outOfBounds.x) {
        maximize(true);
    }
};

const { isDragging, cancel } = useDrag(onDrag, onDrop, { containerRef });

const maximize = async (halfScreen = false) => {
    maximizeWindow(model, halfScreen);

    return;
};
</script>

<template>
<section
    ref="containerRef"
    class="app-header h-10 flex items-center justify-center relative"
    :class="[
        cursor,
        { 'rounded-t-xl': model.roundedBorder }
    ]"
    data-draggable="header"
    @dblclick="maximize(false)">
    <slot />
</section>
</template>