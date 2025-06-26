import { createWebHistory, createRouter } from 'vue-router'

import call from './axios.js';
import env from './usables/env.js';
import routes from './usables/routes.js';

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async (to, from, next) => {
    const token = localStorage.getItem('token');
    if (token) {
        call.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    }
    // // set the locale in the Axios header
    // const locale = localStorage.getItem('locale') ?? 'en';
    // axios.defaults.headers.common['Accept-Language'] = locale;

    // if route requires authentication but token is not set, redirect to login
    if (to.meta.requiresAuth && !token) {
        return next('/login');
    }

    // if route does not requires authentication but token is set, redirect to home
    if (!to.meta.requiresAuth && token) {
        return next('/');
    }
    next();
});

export default router;
