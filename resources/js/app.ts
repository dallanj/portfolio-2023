import '../css/app.scss';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, DefineComponent, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Pinia
import { createPinia } from 'pinia';
const pinia = createPinia();

// Components
import MainLayout from './layouts/MainLayout.vue';
import { useModal } from '@/composables/useModal';

import RegisterGlobalComponents from '@/helpers/registerGlobalComponents';

// FontAwesomeIcons
import * as fontAwesomeConfig from '@/fontAwesomeConfig';
const { library, dom, FontAwesomeIcon, ...faIcons } = fontAwesomeConfig;
library.add(faIcons);

// Components to be registered globally
const globalComponents = {
    Simple: import.meta.glob('./components/Simple/**/*.vue'),
    Modals: import.meta.glob('./modals/**/*.vue'),
};

createInertiaApp({
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./Pages/**/*.vue'),
        ),
    title: (title) => `${title} - ${appName}`,
    setup({ el, App, props, plugin }: { el: any; App: any; props: any; plugin: any }) {
        const app = createApp({ render: () => h(App, props) });

        app.use(plugin)
            .use(pinia)

        // Components
        app.component('MainLayout', MainLayout)
            .provide('modals', useModal())
            .use(ZiggyVue)
            .component('FontAwesomeIcon', FontAwesomeIcon);

        RegisterGlobalComponents(app, globalComponents);
        
        app.mount(el);
    },
});
