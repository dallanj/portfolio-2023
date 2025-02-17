import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

// Pinia
import { createPinia } from 'pinia';
// import { piniaLoader } from '@/utils/pinia';
const pinia = createPinia();
// Components
import MainLayout from './layouts/MainLayout.vue';
import { useModal } from '@/composables/useModal';

import RegisterGlobalComponents from '@/helpers/registerGlobalComponents';
// FontAwesomeIcons
import * as fontAwesomeConfig from '@/fontAwesomeConfig';
const { library, dom, FontAwesomeIcon, ...faIcons } = fontAwesomeConfig;
library.add(faIcons);
// dom.watch();

// Components to be registered globally
const globalComponents = {
    // DataTable: import.meta.glob('./Components/Tables/DataTable/*.vue'),
    Simple: import.meta.glob('./components/Simple/**/*.vue'),
    Modals: import.meta.glob('./modals/**/*.vue'),
};

createInertiaApp({
    resolve: name => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob('./pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        app.use(plugin)
            .use(pinia)

        // Components
        app.component('MainLayout', MainLayout)
            .provide('modals', useModal())
            .component('FontAwesomeIcon', FontAwesomeIcon);

        RegisterGlobalComponents(app, globalComponents);
        // Configurations
        app.config.devtools = true;
        
        app.mount(el);
    },
});
