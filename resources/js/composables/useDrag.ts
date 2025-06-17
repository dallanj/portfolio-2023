import { ref, onMounted, onBeforeUnmount, Ref } from "vue";

interface InitialMousePosition {
    x: number;
    y: number;
    height: number;
    width: number;
    node: any;
    parent: any;
    left: number;
    top: number;
    offset: any;
}

type DragPayload = {
    delta: { x: number; y: number };
    currentMousePosition: { x: number; y: number };
    initialMousePosition: InitialMousePosition;
    target: string | null;
};

type DropPayload = {
    delta: { x: number; y: number };
    initialPosition: { x: number; y: number };
    container?: DOMRect;
    target: string | null;
};

type UseDragOptions = {
    containerRef: Ref<HTMLElement | null>;
};

export function useDrag(
  onDrag: (payload: DragPayload) => void,
  onDrop: (payload: DropPayload) => void,
  { containerRef }: UseDragOptions
) {
    const isDragging = ref(false);
    const cancelDrag = ref(false);
    const draggableKey = ref<string | null>(null);
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
        offset: null as unknown,
    });

    const cancel = () => {
        if (containerRef.value) {
            cancelDrag.value = true;
            // detachListeners(containerRef.value);
        }
    };

    const onPointerDown = (e: PointerEvent) => {
        const target = (e.target as HTMLElement)?.closest("[data-draggable]") as HTMLElement | null;
        if (!target) return;

        isDragging.value = true;
        draggableKey.value = target.getAttribute("data-draggable");

        // Record the initial mouse position
        initialMousePosition.value = {
            x: e.offsetX,
            y: e.offsetY,
            height: (e as any).offsetHeight ?? 0,
            width: (e as any).offsetWidth ?? 0,
            node: (e as any).offsetNode ?? 0,
            parent: (e as any).offsetParent ?? 0,
            left: (e as any).offsetLeft ?? 0,
            top: (e as any).offsetTop ?? 0,
            offset: e,
        };
        lastMousePosition.value = { x: e.clientX, y: e.clientY };

        target.setPointerCapture(e.pointerId);
    };

    const onPointerMove = (e: PointerEvent) => {
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

    const onPointerUp = (e: PointerEvent) => {
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
    const attachListeners = (container: HTMLElement) => {
        container.addEventListener("pointerdown", onPointerDown);
        container.addEventListener("pointermove", onPointerMove);
        container.addEventListener("pointerup", onPointerUp);
        container.addEventListener("pointercancel", onPointerUp);
    };

    const detachListeners = (container: HTMLElement) => {
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
