<template>
    <ScrollToTopButton></ScrollToTopButton>
    <PageLoader v-if="isPageLoading"></PageLoader>
    <AddContact :show="isModalOpen">
    </AddContact>

    <router-link :to="{ name: 'services' }" class="back-btn"><i class="fa-solid fa-arrow-left"></i></router-link>
    <br>
    <br>
    <hr>
    <h2 class="title">Contact <button class="add-new-contact-btn" @click="this.isModalOpen = true">
            <h3>Add New <i class="fa-solid fa-plus"></i></h3>
        </button></h2>

    <div class="contact-search">
        <div class="contact-search-tab">
            <input type="text" placeholder="Search Contact..." @input="debouncedSearch" v-model="search">
            <button v-if="search" @click="() => { this.search = ''; this.debouncedSearch(); }"><i
                    class="fa-solid fa-x"></i></button>
        </div>
    </div>

    <div class="contact-list">
        <router-link v-if="contactList.length > 0" v-for="(contact, index) in contactList"
            :to="{ name: 'payment', params: { fbid: contact.toUser.fbid } }" class="contact-card" :key="index">
            <div class="contact-img">
                <img :src="contact.toUser.avatar" alt="">
            </div>
            <div class="contact-detail">
                <h5>{{ contact.toUser.name }}</h5>
            </div>
        </router-link>
        <div v-else class="empty-state">
            <img :src="asset('images.noSearchEmptyState')" alt="">
            <h2>No Contact Found</h2>
        </div>
    </div>
    <PartLoader v-if="isContactLoading"></PartLoader>
</template>
<script>
import PageLoader from '../layout/PageLoader.vue';
import PartLoader from '../layout/PartLoader.vue';
import AddContact from '../modals/AddContact.vue';
import DOMPurify from "dompurify";
import { debounce } from 'lodash';
import ScrollToTopButton from '../layout/ScrollToTopButton.vue';
export default {
    components: {
        PageLoader,
        PartLoader,
        AddContact,
        ScrollToTopButton
    },
    setup() {
        window.scrollTo(0, 0);
    },
    data() {
        return {
            isPageLoading: true,
            isContactLoading: false,
            currentPage: 1,
            perPage: 15,
            lastPage: 1,
            search: '',
            contactList: [],
            isModalOpen: false
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
            this.contactList = [];

            this.isPageLoading = true;
            await this.getContactList(true);
        },
        async reFetchListAfterAddingNewContact() {
            this.search = '';
            await this.searchContact();
        },
        async getContactList(isInitialLoad = false) {
            try {
                if (!this.isApiCalled && this.currentPage <= this.lastPage) {
                    this.isApiCalled = true;

                    if (!isInitialLoad) {
                        this.isContactLoading = true;
                    }

                    let url = this.api('contact.list', { currentPage: this.currentPage, perPage: this.perPage });

                    url = this.search ? url + '/' + DOMPurify.sanitize(this.search) : url;

                    await this.call.get(url).then(response => (
                        this.contactList = this.contactList.length > 0 ? [...this.contactList, ...response.data.data] : response.data.data,
                        this.lastPage = response.data.meta.lastPage,
                        this.currentPage++,
                        this.isApiCalled = false,
                        isInitialLoad ? this.isPageLoading = false : this.isContactLoading = false
                    ));
                }
            } catch (e) {
                this.isApiCalled = false;
                isInitialLoad ? this.isPageLoading = false : this.isContactLoading = false;
            }
        }
    },
    async created() {
        await this.getContactList(true);
    },
    mounted() {
        document.addEventListener('scroll', () => {
            const footer = document.getElementsByTagName('footer')[0];
            const footerTop = footer.getBoundingClientRect().top;
            const windowHeight = window.innerHeight;

            if (footerTop <= windowHeight && this.currentPage <= this.lastPage) {
                this.getContactList();
            }
        });
    }
}
</script>
