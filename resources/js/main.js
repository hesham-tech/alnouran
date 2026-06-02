import '../css/app.css';
import '../css/design_tokens.css';
import { createPinia } from 'pinia';
import { createApp } from 'vue';
import App from './App.vue';
import PopupComponent from './components/PopupComponent.vue';
import router from './router';
import 'vuetify/styles';
import 'animate.css';
import { createVuetify } from 'vuetify';
import * as components from 'vuetify/components';
import * as directives from 'vuetify/directives';
import '@mdi/font/css/materialdesignicons.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';
import { createI18n } from 'vue-i18n';
import enuc from './locales/en.json';
import areg from './locales/ar.json';
import { ar } from 'vuetify/locale';
if (!localStorage.defaultTheme) {
  localStorage.setItem('defaultTheme', false);
}
const vuetify = createVuetify({
  theme: {
    defaultTheme: localStorage.defaultTheme == 'true' ? 'dark' : 'light',
    themes: {
      light: {
        colors: {
          primary: '#4f46e5',
          secondary: '#64748b',
          accent: '#818cf8',
          error: '#ef4444',
          info: '#0ea5e9',
          success: '#10b981',
          warning: '#f59e0b',
          background: '#f8fafc',
          surface: '#ffffff',
        },
      },
      dark: {
        colors: {
          primary: '#818cf8',
          secondary: '#94a3b8',
          background: '#0f172a',
          surface: '#1e293b',
        },
      },
    },
  },
  defaults: {
    VCard: {
      flat: true,
      border: true,
      density: 'compact',
    },
    VBtn: {
      rounded: 'md',
      density: 'comfortable',
    },
    VTextField: {
      variant: 'outlined',
      density: 'compact',
      hideDetails: 'auto',
    },
    VSelect: {
      variant: 'outlined',
      density: 'compact',
      hideDetails: 'auto',
    },
  },
  locale: {
    locale: 'ar',
    fallback: 'en',
    messages: {
      ar,
    },
  },
  components,
  directives,
});
const i18n = createI18n({
  locale: 'ar',
  messages: {
    en: enuc,
    ar: areg,
  },
});
createApp(App)
  .component('popup', PopupComponent)
  .use(i18n)
  .use(router)
  .use(vuetify)
  .use(createPinia())
  .mount('#app');
