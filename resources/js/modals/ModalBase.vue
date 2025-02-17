<template>
<Teleport to="body">
    <div ref="overlay" class="simple-modal__overlay" :style="position">
        <!-- Transition only the modal content, not the overlay -->
        <Transition
            enter-active-class="ease-out duration-300"
            enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            enter-to-class="opacity-100 translate-y-0 sm:scale-100"
            leave-active-class="ease-in duration-200"
            leave-from-class="opacity-100 translate-y-6 sm:scale-100"
            leave-to-class="opacity-0 translate-y-12 sm:translate-y-0 sm:scale-95"
            appear
            @after-leave="finalizeClose">
            <div v-if="!closing" ref="wrapper" class="mt-6 overflow-hidden">
                <component
                    :is="activeModal"
                    v-bind="modalProps"
                    @close="startClosing" />
            </div>
        </Transition>
    </div>
</Teleport>
</template>
<script setup>
import { ref, watch, onMounted, onUnmounted, computed, inject } from 'vue';
// import { useErrorStore } from '@/stores/error';
// const { $reset } = useErrorStore();

// Composables
const { activeModal, modalProps, closeModal } = inject('modals');

// References for DOM elements
const wrapper = ref(null);
const overlay = ref(null);

// For opening/closing animations
const animation = ref(false);
const closing = ref(false);

// Promised response from a modal
let promise = null;

// Start closing animation
const startClosing = (response = null) => {
    closing.value = true;
    // Trigger the leave animation
    animation.value = false;
    // Store the modal response to be returned
    promise = response;
};

// Finalize the modal closing after animation
const finalizeClose = _ => {
    closing.value = false;
    // Finally close the modal after animation finishes and return a promise if applicable
    closeModal(promise);
    promise = null;
    // $reset();
};

// Close the modal using the escape key
const closeOnEscape = e => {
    if (animation.value && e.key === 'Escape') {
        startClosing();
    }
};

// Close the modal by clicking the overlay
const closeOnClickOutside = e => {
    if (wrapper.value && !wrapper.value.contains(e.target) && overlay.value && overlay.value.contains(e.target)) {
        startClosing();
    }
};

// Watch active modal to handle opening
watch(_ => activeModal.value, (modal) => {
    // If the modal is active, trigger the enter animation
    modal && setTimeout(_ => { animation.value = true }, 300);
});

const position = ref('');

onMounted(() => {
    document.addEventListener('keydown', closeOnEscape);
    document.addEventListener('click', closeOnClickOutside);
    console.log('mounted', modalProps.value.position);
    document.documentElement.classList.add('fixed');
    position.value = modalProps.value.position;
});

onUnmounted(() => {
    document.removeEventListener('keydown', closeOnEscape)
    document.removeEventListener('click', closeOnClickOutside);
    console.log(document.documentElement.classList)
    document.documentElement.classList.remove('fixed');
    position.value = '';
});
</script>
    