import './bootstrap';
import '../css/tailwind.css';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import Toast from 'vue-toastification';
import 'vue-toastification/dist/index.css';
import App from './App.vue';
import router from './router';

const app = createApp(App);
const pinia = createPinia();

const toastOptions = {
  position: 'top-right',
  timeout: 3000,
  closeOnClick: true,
  pauseOnFocusLoss: true,
  pauseOnHover: true,
  draggable: true,
  draggablePercent: 0.6,
  showCloseButtonOnHover: false,
  hideProgressBar: true,
  closeButton: 'button',
  icon: true,
  rtl: false,
  transition: 'Vue-Toastification__bounce',
  maxToasts: 3,
  newestOnTop: true,
  toastClassName: 'custom-toast',
  bodyClassName: ['custom-toast-body'],
  containerClassName: 'custom-toast-container'
};

app.use(Toast, toastOptions);
app.use(pinia);
app.use(router);
app.mount('#app');
