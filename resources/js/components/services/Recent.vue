<style scoped>
h2 {
    margin-top: 1rem;
}
</style>
<template>
    <ScrollToTopButton></ScrollToTopButton>
    <PageLoader v-if="isPageLoading"></PageLoader>

    <router-link :to="{ name: 'services' }" class="back-btn"><i class="fa-solid fa-arrow-left"></i></router-link>
    <br>
    <br>
    <hr>
    <h2 class="title">Recent</h2>

    <div class="contact-list">
        <router-link v-if="recentList.length > 0" v-for="(recent, index) in recentList"
            :to="{ name: 'recent_payment', params: { fbid: recent.fbid } }" class="contact-card" :key="index">
            <div class="contact-img">
                <img :src="recent.avatar" alt="">
            </div>
            <div class="contact-detail">
                <h5>{{ recent.name }}</h5>
            </div>
        </router-link>
        <div v-else class="empty-state">
            <img :src="asset('images.noSearchEmptyState')" alt="">
            <h2>No Recent Found</h2>
        </div>
    </div>
    <PartLoader v-if="isRecentLoading"></PartLoader>
</template>
<script>
import PageLoader from '../layout/PageLoader.vue';
import PartLoader from '../layout/PartLoader.vue';
import { debounce } from 'lodash';
import ScrollToTopButton from '../layout/ScrollToTopButton.vue';
export default {
    components: {
        PageLoader,
        PartLoader,
        ScrollToTopButton
    },
    setup() {
        window.scrollTo(0, 0);
    },
    data() {
        return {
            isPageLoading: true,
            isRecentLoading: false,
            currentPage: 1,
            perPage: 15,
            lastPage: 1,
            recentList: [],
        }
    },
    methods: {
        debouncedSearch: debounce(function () {
            this.searchContact();
        }, 500),
        async searchContact() {
            this.currentPage = 1;
            this.perPage = 15;
            this.lastPage = 1;
            this.recentList = [];

            this.isPageLoading = true;
            await this.getRecentList(true);
        },
        async reFetchListAfterAddingNewContact() {
            this.search = '';
            await this.searchContact();
        },
        async getRecentList(isInitialLoad = false) {
            try {
                if (!this.isApiCalled && this.currentPage <= this.lastPage) {
                    this.isApiCalled = true;

                    if (!isInitialLoad) {
                        this.isRecentLoading = true;
                    }

                    let url = this.api('recent.list', { currentPage: this.currentPage, perPage: this.perPage });

                    url = this.search ? url + '/' + this.search : url;

                    await this.call.get(url).then(response => (
                        this.recentList = this.recentList.length > 0 ? [...this.recentList, ...response.data.data] : response.data.data,
                        this.lastPage = response.data.meta.lastPage,
                        this.currentPage++,
                        this.isApiCalled = false,
                        isInitialLoad ? this.isPageLoading = false : this.isRecentLoading = false
                    ));
                }
            } catch (e) {
                this.isApiCalled = false;
                isInitialLoad ? this.isPageLoading = false : this.isRecentLoading = false;
            }
        }
    },
    async created() {
        await this.getRecentList(true);
    },
    mounted() {
        document.addEventListener('scroll', () => {
            const footer = document.getElementsByTagName('footer')[0];
            const footerTop = footer.getBoundingClientRect().top;
            const windowHeight = window.innerHeight;

            if (footerTop <= windowHeight && this.currentPage <= this.lastPage) {
                this.getRecentList();
            }
        });
    }
}
</script>
