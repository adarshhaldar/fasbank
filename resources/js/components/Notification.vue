<template>
    <ScrollToTopButton></ScrollToTopButton>
    <PageLoader v-if="isPageLoading"></PageLoader>

    <h2 class="title">Notifications <button v-if="notificationList.length" class="del-all-btn" @click="deleteAllNotification">
            <h3>Delete All <i class="fa-solid fa-trash-can"></i></h3>
        </button></h2>

    <div v-if="notificationList.length > 0" class="notification-list">
        <div class="notification-list-body" v-for="(notification, index) in notificationList" :key="index">
            <h3 class="notification-date-group"
                v-if="index == 0 || notificationList[index - 1].groupDate != notification.groupDate"><i
                    class="fa-solid fa-calendar-days"></i> {{
                        notification.groupDate }}</h3>
            <div class="notification-card">
                <router-link
                    :to="{ name: notification.slug == 'transfer' || notification.slug == 'request' ? 'recent_payment' : 'service', params: { fbid: notification.slug == 'transfer' || notification.slug == 'request' ? notification.dataId : null } }">
                    <h3>{{ notification.title }}</h3>
                    <p>{{ notification.body }}</p>
                    <small><i class="fa-regular fa-clock"></i> {{ notification.createdAt }}</small>
                </router-link>
                <button class="notification-del-btn"><i class="fa-solid fa-trash-can"
                        @click="deleteNotification(notification.id)"></i></button>
            </div>
        </div>
        <PartLoader v-if="isNotificationLoading"></PartLoader>
    </div>

    <div v-else class="empty-state">
        <img :src="asset('images.noNotificationEmptyState')" alt="">
        <h2>No Notification Found</h2>
    </div>
</template>
<script>
import { toast } from 'vue3-toastify';
import PageLoader from './layout/PageLoader.vue';
import PartLoader from './layout/PartLoader.vue';
import ScrollToTopButton from './layout/ScrollToTopButton.vue';
export default {
    setup() {
        window.scrollTo(0, 0);
    },
    components: {
        PageLoader,
        PartLoader,
        ScrollToTopButton
    },
    data() {
        return {
            isPageLoading: true,
            isNotificationLoading: false,
            currentPage: 1,
            perPage: 15,
            lastPage: 1,
            notificationList: []
        }
    },
    methods: {
        removeNotificationFromList(id) {
            let index = this.notificationList.findIndex(obj => obj.id === id);
            if (index !== -1) {
                this.notificationList.splice(index, 1);
            }
        },
        async deleteNotification(id) {
            try {
                if (!this.isApiCalled) {
                    this.isApiCalled = true;

                    await this.call.get(this.api('notifications.delete', { id: id })).then(response => (
                        toast.success(response.data.message),
                        this.removeNotificationFromList(id)
                    ));
                }
            } catch (e) {
                this.isApiCalled = false;
            }
        },
        async deleteAllNotification() {
            try {
                if (!this.isApiCalled) {
                    this.isApiCalled = true;

                    await this.call.get(this.api('notifications.deleteAll')).then(response => (
                        toast.success(response.data.message),
                        this.notificationList = []
                    ));
                }
            } catch (e) {
                this.isApiCalled = false;
            }
        },
        async getNotificationList(isInitialLoad = false) {
            try {
                if (!this.isApiCalled && this.currentPage <= this.lastPage) {

                    this.isApiCalled = true;

                    if (!isInitialLoad) {
                        this.isNotificationLoading = true;
                    }

                    let url = this.api('notifications.list', { currentPage: this.currentPage, perPage: this.perPage });

                    await this.call.get(url).then(response => (
                        this.notificationList = this.notificationList.length > 0 ? [...this.notificationList, ...response.data.data] : response.data.data,
                        this.lastPage = response.data.meta.lastPage,
                        this.currentPage++,
                        this.isApiCalled = false,
                        isInitialLoad ? this.isPageLoading = false : this.isNotificationLoading = false
                    ));
                }
            } catch (e) {
                this.isApiCalled = false;
                isInitialLoad ? this.isPageLoading = false : this.isNotificationLoading = false;
            }
        }
    },
    async created() {
        await this.getNotificationList(true);
    },
    mounted() {
        document.addEventListener('scroll', () => {
            const footer = document.getElementsByTagName('footer')[0];
            const footerTop = footer.getBoundingClientRect().top;
            const windowHeight = window.innerHeight;

            if (footerTop <= windowHeight && this.currentPage <= this.lastPage) {
                this.getNotificationList();
            }
        });
    }
}
</script>
