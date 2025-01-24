import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

// Pinia
import { createPinia } from 'pinia';
// import { piniaLoader } from '@/utils/pinia';
const pinia = createPinia();
// Components
import MainLayout from './layouts/MainLayout.vue';

createInertiaApp({
    resolve: name => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob('./pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        app.use(plugin)
            .use(pinia)

        // Components
        app.component('MainLayout', MainLayout);

        // Configurations
        app.config.devtools = true;
        
        app.mount(el);
    },
});
