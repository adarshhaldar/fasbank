<template>
    <NavBar :newNotificationCount="newNotificationCount"></NavBar>
    <main class="main-content">
        <router-view :key="$route.fullPath"></router-view>
    </main>
    <Footer></Footer>
</template>

<script>
import { toast } from 'vue3-toastify';
import NavBar from './layout/NavBar.vue';
import Footer from './layout/Footer.vue';
import PageLoader from './layout/PageLoader.vue';

export default {
    components: {
        NavBar,
        Footer,
        PageLoader
    },
    data() {
        return {
            newNotificationCount: 0
        }
    },
    setup() {
        window.scrollTo(0, 0);
    },
    created() {
        try {
            const token = localStorage.getItem('token');

            if (token) {
                this.call.get(this.api('account.detail'), {
                    headers: {
                        "Authorization": `Bearer ${token}`
                    }
                }).then(response => (
                    this.userState.setUser(response.data.data)
                ));

                let notification = null;

                this.call.get(this.api('notifications.new'), {
                    headers: {
                        "Authorization": `Bearer ${token}`
                    }
                }).then(response => (
                    response.data.newNotification ?
                        (
                            notification = new Notification(response.data.newNotification.title, {
                                body: response.data.newNotification.body,
                                icon: this.asset('images.logo')
                            }),

                            notification.onclick = function () {
                                window.location.href = response.data.newNotification.url;
                            }) : true,

                    this.newNotificationCount = response.data.newNotificationCount
                ));

                setInterval(() => {
                    this.call.get(this.api('notifications.new'), {
                        headers: {
                            "Authorization": `Bearer ${token}`
                        }
                    }).then(response => (
                        response.data.newNotification ?
                            (
                                notification = new Notification(response.data.newNotification.title, {
                                    body: response.data.newNotification.body,
                                    icon: this.asset('images.moneyRequest')
                                }),

                                notification.onclick = function () {
                                    window.location.href = response.data.newNotification.url;
                                }) : true,

                        this.newNotificationCount = response.data.newNotificationCount
                    ));
                }, 30000);
            }
        } catch (e) {
            toast.error(e)
        }
    },
}
</script>
