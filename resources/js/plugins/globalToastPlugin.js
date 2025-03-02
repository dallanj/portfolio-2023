// Import Toast, POSITION Enum, and useToast method from vue-toastification
import Toast, { POSITION, useToast } from 'vue-toastification';

// Define the toast options
const toastOptions = {
    timeout: 5000,
    position: POSITION.BOTTOM_RIGHT,
    showCloseButtonOnHover: true,
    toastClassName: 'cursor-pointer',
};

export default {
    install: (app) => {
        // Global plugin usage across script components
        app.use(Toast, toastOptions);
        const toast = useToast();
        
        // Composition API Usage within <script setup> components
        window.$toast = toast; // Usage: $toast.info('This is a info message!');
        // Optional API Usage within <script> components
        app.config.globalProperties.$toast = toast; // Usage: this.$toast.info('This is a info message!');
    }
};