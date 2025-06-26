<template>
    <nav class="navbar">
        <div class="container">
            <router-link :to="{ name: 'dashboard' }" class="logo"><img :src="asset('images.logo')" :alt="env('APP_NAME')">FasBank</router-link>
            <div class="mob-toggle">
                <router-link id="notification-mob-nav-link"
                    :style="newNotificationCount > 0 ? 'color:rgb(19, 185, 94)' : 'color:white'"
                    :to="{ name: 'notification' }"><i id="notification-bell-icon" class="fa-solid fa-bell"
                        :class="newNotificationCount > 0 ? 'fa-shake' : ''"></i>
                </router-link>
                <button class="menu-toggle" @click="toggleMenu()"><i class="fa-solid"
                        :class="isMenuOpen ? 'fa-xmark' : 'fa-bars'"></i></button>
            </div>
            <ul class="nav-links">
                <li id="notification-nav-link" v-if="this.$route.meta.requiresAuth"><router-link
                        :style="newNotificationCount > 0 ? 'color:rgb(19, 185, 94)' : 'color:white'"
                        :to="{ name: 'notification' }"><i id="notification-bell-icon" class="fa-solid fa-bell"
                            :class="newNotificationCount > 0 ? 'fa-shake' : ''"></i>
                    </router-link>
                </li>
                <li v-if="this.$route.meta.requiresAuth"><router-link :to="{ name: 'dashboard' }"
                        :class="this.$route.name == 'dashboard' ? 'active' : ''"><i class="fa-solid fa-chart-area"></i>
                        Dashboard</router-link></li>

                <li v-if="this.$route.meta.requiresAuth"><router-link :to="{ name: 'services' }"
                        :class="this.$route.name == 'transfer_money' || this.$route.name == 'request_money' || this.$route.name == 'recent_payment' || this.$route.name == 'transactions' || this.$route.name == 'services' || this.$route.name == 'recent' || this.$route.name == 'contact' || this.$route.name == 'payment' ? 'active' : ''"><i
                            class="fa-brands fa-servicestack"></i> Services</router-link></li>
                <li v-if="this.$route.meta.requiresAuth == false"><router-link :to="{ name: 'login' }"
                        :class="this.$route.name == 'login' ? 'active' : ''">Login</router-link>
                </li>
                <li v-if="this.$route.meta.requiresAuth"><router-link :to="{ name: 'account' }"
                        :class="this.$route.name == 'account' ? 'active' : ''"><i class="fa-solid fa-user"></i>
                        Account</router-link></li>

            </ul>
        </div>
    </nav>
</template>
<script>
export default {
    props: {
        newNotificationCount: {
            type: Number,
            required: true
        }
    },
    data() {
        return {
            isMenuOpen: false,
        }
    },
    methods: {
        toggleMenu() {
            const navLinks = document.querySelector('.nav-links');
            this.isMenuOpen = navLinks.style.display == 'flex' ? false : true;
            navLinks.style.display = navLinks.style.display == 'flex' ? 'none' : 'flex';
        }
    }
}
</script>
