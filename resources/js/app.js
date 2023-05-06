import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

// Components
import MainLayout from './layouts/MainLayout.vue';

createInertiaApp({
    resolve: name => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob('./pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        // Components
        app.component('MainLayout', MainLayout);

        app.use(plugin).mount(el);
    },
});
