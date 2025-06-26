const routes = [
    {
        path: '/',
        name: 'dashboard',
        component: () => import('./../components/Dashboard.vue'),
        meta: { title: 'Dashboard', requiresAuth: true }
    },
    {
        path: '/login',
        name: 'login',
        component: () => import('./../components/auth/Login.vue'),
        meta: { title: 'Login', requiresAuth: false }
    },
    {
        path: '/services',
        name: 'services',
        component: () => import('./../components/services/Index.vue'),
        meta: { title: 'Services', requiresAuth: true }
    },
    {
        path: '/recent',
        name: 'recent',
        component: () => import('./../components/services/Recent.vue'),
        meta: { title: 'Recent', requiresAuth: true }
    },
    {
        path: '/transactions',
        name: 'transactions',
        component: () => import('./../components/services/Transactions.vue'),
        meta: { title: 'Transactions', requiresAuth: true }
    },
    {
        path: '/contact',
        name: 'contact',
        component: () => import('./../components/services/Contact.vue'),
        meta: { title: 'Contact', requiresAuth: true }
    },
    {
        path: '/notification',
        name: 'notification',
        component: () => import('./../components/Notification.vue'),
        meta: { title: 'Notification', requiresAuth: true }
    },
    {
        path: '/account',
        name: 'account',
        component: () => import('./../components/Account.vue'),
        meta: { title: 'Account', requiresAuth: true }
    },
    {
        path: '/payment/:fbid',
        name: 'payment',
        component: () => import('./../components/services/Payment.vue'),
        meta: { title: 'Payment', requiresAuth: true }
    },
    {
        path: '/recent-payment/:fbid',
        name: 'recent_payment',
        component: () => import('./../components/services/Payment.vue'),
        meta: { title: 'Payment', requiresAuth: true }
    },
    {
        path: '/request-money',
        name: 'request_money',
        component: () => import('./../components/services/RequestMoney.vue'),
        meta: { title: 'Request Money', requiresAuth: true }
    },
    {
        path: '/transfer-money',
        name: 'transfer_money',
        component: () => import('./../components/services/TransferMoney.vue'),
        meta: { title: 'Transfer Money', requiresAuth: true }
    },
    {
        path: '/:pathMatch(.*)*',
        component: () => import('./../components/error/PageNotFound.vue')
    }
];

export default routes;
