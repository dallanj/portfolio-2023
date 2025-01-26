<template>
<article
    :ref="`${application.data.value}-application`"
    :id="`${application.data.value}-application`"
    class="app-window fixed block"
    :class="[
      cursor,
      { 'rounded-t-xl': application.roundedBorder },
      { 'z-40': application === getActiveWindow && Object.values(application.outOfBounds).some(val => val === true) }
    ]"
    :style="{
        width: windowWidth,
        height: windowHeight,
        top: application.top + 'px',
        left: application.left + 'px'
    }"
    @mousedown="startActions"
    @mouseup="stopResize"
    @mousemove="setCursor">

    <ApplicationWindowHeader
        v-model="application"
        @dragging-positions="draggingPositions">
        <h2 class="select-none">{{ application.data.label }}</h2>
        <ApplicationWindowActions 
            v-model="application" />
    </ApplicationWindowHeader>
</article>
</template>
    
<script setup>
import { ref, computed, watch } from 'vue';
import { useActivitiesStore } from '@/stores/activities';
import ApplicationWindowHeader from './ApplicationWindowHeader.vue';
import ApplicationWindowActions from './ApplicationWindowActions.vue';

import actionsMixin from '@/mixins/actionsMixin'; // Mixin logic can be directly adapted or integrated.
import { useApplicationVisibility } from '@/composables/useApplicationVisibility.vue';

const { hasClickedOutside, toggleApplicationVisibility, isApplicationVisible } = useApplicationVisibility();

const props = defineProps({
  activity: {
    type: Object,
    required: true,
  },
});

const application = ref(props.activity);

const {
    getActiveWindow,
} = useActivitiesStore();

const activitiesStore = useActivitiesStore();

// Reactive state
const startX = ref(0);
const startY = ref(0);
const startWidth = ref(0);
const startHeight = ref(0);
const startTop = ref(0);
const startLeft = ref(0);
const minWidth = ref(50);
const minHeight = ref(50);
const width = ref(200);
const height = ref(200);
const top = ref(100);
const left = ref(300);
const direction = ref(null);
const cursor = ref('cursor-default');
const boundary = ref({ x: 80, y: 32 });

// Computed properties
const windowWidth = computed(() => (Number.isInteger(application.value.width) ? `${application.value.width}px` : application.value.width));
const windowHeight = computed(() => (Number.isInteger(application.value.height) ? `${application.value.height}px` : application.value.height));

// Methods
function startActions(event) {
  activitiesStore.setActiveWindow(application.value);

  if (!event.target.classList.contains('app-window')) return;

  startResize(event);
}

function setCursor(event) {
  if (!event.target.classList.contains('app-window')) {
    cursor.value = 'cursor-default';
    return;
  }

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

function startResize(event) {
  event.preventDefault();

  if (!event.target.classList.contains('app-window')) return;

  startX.value = event.clientX;
  startY.value = event.clientY;
  startWidth.value = width.value;
  startHeight.value = height.value;
  startTop.value = top.value;
  startLeft.value = left.value;
  direction.value = findDirection(event);

  document.addEventListener('mousemove', resize);
  document.addEventListener('mouseup', stopResize);
}

function resize(event) {
  const deltaX = event.clientX - startX.value;
  const deltaY = event.clientY - startY.value;

  switch (direction.value) {
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

function findDirection(event) {
  let dir = '';

  if (event.offsetX < 10) dir = 'left';
  else if (event.offsetX > width.value - 10) dir = 'right';

  if (event.offsetY < 10) dir += dir ? '-top' : 'top';
  else if (event.offsetY > height.value - 10) dir += dir ? '-bottom' : 'bottom';

  return dir;
}

function dragRightBottom(mousePosChange, isWidth = true) {
  if (isWidth) {
    width.value = Math.max(startWidth.value + mousePosChange, minWidth.value);
  } else {
    height.value = Math.max(startHeight.value + mousePosChange, minHeight.value);
  }
}

function dragLeftTop(mousePosChange, isLeft = true) {
  const startSize = isLeft ? startWidth.value : startHeight.value;
  const minSize = isLeft ? minWidth.value : minHeight.value;
  const startPosition = isLeft ? startLeft.value : startTop.value;

  const newSize = Math.max(startSize - mousePosChange, minSize);
  if (isLeft) {
    left.value = Math.max(startPosition + mousePosChange, boundary.value.x);
    width.value = newSize;
  } else {
    top.value = Math.max(startPosition + mousePosChange, boundary.value.y);
    height.value = newSize;
  }
}

function stopResize() {
  document.removeEventListener('mousemove', resize);
  document.removeEventListener('mouseup', stopResize);
}

function draggingPositions(positions) {
  left.value = positions.x;
  top.value = positions.y;
}

function closeActivity(event) {
    console.log('closeActivity', event)
}

watch(application.value, closeActivity);
</script>
