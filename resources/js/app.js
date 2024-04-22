import './bootstrap';
import '../css/app.css';
import '../scss/app.scss';
import '../fonts/tanha/Farsi-Digits/font-face.css';
import '../fonts/shabnam/Farsi-Digits/font-face.css';
import 'tw-elements';
import 'lity';
import 'lity/dist/lity.min.css';
import {createApp, h} from 'vue';
import {createInertiaApp} from '@inertiajs/vue3';
import {resolvePageComponent} from 'laravel-vite-plugin/inertia-helpers';
import {ZiggyVue} from '../../vendor/tightenco/ziggy/dist/vue.m';
import Mixins from "@/vue-mixins";
import LoadScript from 'vue-plugin-load-script';
import VueDragscroll from "vue-dragscroll";

import './scripts';
import mitt from 'mitt';

const emitter = mitt();
const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({el, App, props, plugin}) {

        window.Vue = createApp({render: () => h(App, props)})
            // .component('Toast', Toast)
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .use(LoadScript)
            .use(VueDragscroll)
            .mixin(Mixins);
        window.Vue.config.globalProperties.emitter = emitter;
        window.Vue.mount(el);
        return window.Vue;
    },
    progress: {
        color: '#aa1111',
    },
});



