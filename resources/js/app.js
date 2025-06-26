import './bootstrap';
// import 'bootstrap/dist/css/bootstrap.min.css';
// import 'bootstrap/dist/js/bootstrap.bundle.min.js';

import { createApp } from 'vue';

import App from './components/App.vue';

// api call axios
import call from './axios.js';

// toastr
import 'vue3-toastify/dist/index.css';
import Vue3Toastify from 'vue3-toastify';

// importing router
import router from './router.js';
// importing utilities
import env from './usables/env.js';
import api from './usables/api.js';
import asset from './usables/asset.js';
// importing user state
import userState from './usables/states/user.js';

const app = createApp(App);

// registering global utility
app.config.globalProperties.env = env;
app.config.globalProperties.api = api;
app.config.globalProperties.call = call;
app.config.globalProperties.asset = asset;
app.config.globalProperties.userState = userState;
app.config.globalProperties.isApiCalled = false;

// setting up toastr
app.use(Vue3Toastify, { theme: 'light' });

app.use(router).mount("#app")
