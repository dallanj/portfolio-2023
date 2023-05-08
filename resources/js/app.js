import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

// Components
import MainLayout from './layouts/MainLayout.vue';

// Plugins
import vClickOutside from 'click-outside-vue3';

createInertiaApp({
    resolve: name => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob('./pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        // Components
        app.component('MainLayout', MainLayout);

        // Plugins
        app.use(vClickOutside);

        // Configurations
        app.config.devtools = true;
        
        app.use(plugin).mount(el);
    },
});
