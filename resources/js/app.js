import './bootstrap';
import { createApp } from 'vue';
import App from './components/App.vue';

import router from './router';

import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';
import '../css/app.css';
import { mask } from 'vue-the-mask';

import axios from 'axios';
import VueAxios from 'vue-axios';

const app = createApp(App);
app.directive('mask', mask);
app.use(VueAxios, axios);
app.use(router);
app.mount('#app');
