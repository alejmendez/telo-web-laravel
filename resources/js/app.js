import './bootstrap';
import 'primeicons/primeicons.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { ZiggyVue } from 'ziggy-js';
import { initLibs } from '@Core/Libs';

// php artisan ziggy:generate --url=https://telochile.cl/
import { Ziggy } from './ziggy.js';

const appName = import.meta.env.VITE_APP_NAME || 'Telo';
const isLocal = import.meta.env.VITE_APP_ENV === 'local';

// Importamos todos los módulos de una vez
const modulePages = import.meta.glob('./../../Modules/*/Resources/Pages/**/*.vue', { eager: true });

const resolvePageComponent = (name) => {
  const [module, pageName] = name.split('::');
  const pagePath = `../../Modules/${module}/Resources/Pages/${pageName}.vue`;

  if (!modulePages[pagePath]) {
    throw new Error(`Page "${pagePath}" not found`);
  }

  const page = modulePages[pagePath];

  return typeof page === 'function' ? page() : page;
};

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) => resolvePageComponent(name),
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) });
    initLibs(app);
    app.use(plugin);

    if (isLocal) {
      app.use(ZiggyVue);
    } else {
      app.use(ZiggyVue, Ziggy);
    }

    return app.mount(el);
  },
  progress: {
    color: '#4B5563',
  },
});
