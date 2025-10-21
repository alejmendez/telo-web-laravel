import PrimeVue from 'primevue/config';
import primeLocale from '@Core/Lang/es/prime';

// import Presents from './PrimePresents';
import Aura from '@primeuix/themes/aura';

import Quill from 'quill';
import { Mention, MentionBlot } from 'quill-mention';
import 'quill-mention/dist/quill.mention.min.css';

export const initPrime = (app) => {
  const theme = localStorage.getItem('theme') || 'CarrotOrange';

  Quill.register({ 'blots/mention': MentionBlot, 'modules/mention': Mention });

  app.use(PrimeVue, {
    theme: {
      preset: Aura,
      options: {
        prefix: 'p',
        darkModeSelector: '.dark',
        cssLayer: false,
      },
    },
    locale: primeLocale,
  });
};
