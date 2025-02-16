import { ref, onMounted, onBeforeUnmount } from "vue";

export function useDrag(onDrag, onDrop, { containerRef }) {
  const isDragging = ref(false);
  const cancelDrag = ref(false);
  const draggableKey = ref(null);
  const lastMousePosition = ref({ x: 0, y: 0 });
  const initialMousePosition = ref({
      x: 0,
      y: 0,
      height: 0,
      width: 0,
      node: 0,
      parent: 0,
      left: 0,
      top: 0,
      offset: null,
  });

  const cancel = () => {
    if (containerRef.value) {
        cancelDrag.value = true;
        // detachListeners(containerRef.value);
    }
    
  };

  const onPointerDown = (e) => {
    const target = e.target.closest("[data-draggable]");
    if (!target) return;

    isDragging.value = true;
    draggableKey.value = target.getAttribute("data-draggable");

    // Record the initial mouse position
    initialMousePosition.value = {
        x: e.offsetX,
        y: e.offsetY,
        height: e.offsetHeight,
        width: e.offsetWidth,
        node: e.offsetNode,
        parent: e.offsetParent,
        left: e.offsetLeft,
        top: e.offsetTop,
        offset: e,
    };
    lastMousePosition.value = { x: e.clientX, y: e.clientY };

    target.setPointerCapture(e.pointerId);
  };

  const onPointerMove = (e) => {
    if (!isDragging.value || cancelDrag.value) return;

    const currentMousePosition = { x: e.clientX, y: e.clientY };
    const delta = {
      x: currentMousePosition.x - lastMousePosition.value.x,
      y: currentMousePosition.y - lastMousePosition.value.y,
    };

    onDrag({
      delta,
      currentMousePosition: currentMousePosition,
      initialMousePosition: initialMousePosition.value,
      target: draggableKey.value,
    });

    // Update the last known position
    lastMousePosition.value = currentMousePosition;
  };

  const onPointerUp = (e) => {
    if (!isDragging.value) return;

    if (cancelDrag.value) {
      isDragging.value = false;
      cancelDrag.value = false; // Reset cancel state
      return;
    }

    const container = containerRef.value?.getBoundingClientRect();
    const delta = {
      x: e.clientX - lastMousePosition.value.x,
      y: e.clientY - lastMousePosition.value.y,
    };

    onDrop({
      delta,
      initialPosition: lastMousePosition.value,
      container,
      target: draggableKey.value,
    });

    isDragging.value = false;
    draggableKey.value = null;
  };

  // Attach event listeners
  const attachListeners = (container) => {
    container.addEventListener("pointerdown", onPointerDown);
    container.addEventListener("pointermove", onPointerMove);
    container.addEventListener("pointerup", onPointerUp);
    container.addEventListener("pointercancel", onPointerUp);
  };

  const detachListeners = (container) => {
    container.removeEventListener("pointerdown", onPointerDown);
    container.removeEventListener("pointermove", onPointerMove);
    container.removeEventListener("pointerup", onPointerUp);
    container.removeEventListener("pointercancel", onPointerUp);
  };

  // Manage lifecycle
  onMounted(() => {
    if (containerRef.value) {
      attachListeners(containerRef.value);
    }
  });

  onBeforeUnmount(() => {
    if (containerRef.value) {
      detachListeners(containerRef.value);
    }
  });

  return {
    isDragging,
    cancel
  };
}
