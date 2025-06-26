<style scoped>
h2 {
    display: flex;
    align-items: center;
}

.empty-state {
    margin-top: 1rem;
}
</style>
<template>
    <PageLoader v-if="isPageLoading"></PageLoader>
    <ScrollToBottomButton></ScrollToBottomButton>
    <PayOrRequestFromContact :modalType="modalType" :show="isModalOpen"
        :toUserName="toUserDetail ? toUserDetail.name : ''" :fbid="this.$route.params.fbid">
    </PayOrRequestFromContact>
    <PaymentDetail :show="isPaymentDetailModalOpen" :payment="currentPaymentDetail ?? {}"
        :toUserDetail="toUserDetail ?? {}"></PaymentDetail>

    <router-link :to="{ name: $route.name == 'payment' ? 'contact' : 'recent' }" class="back-btn"><i
            class="fa-solid fa-arrow-left"></i></router-link>
    <br>
    <br>
    <hr>

    <div v-if="paymentList.length > 0" class="payment-history">
        <PartLoader v-if="isPaymentLoading"></PartLoader>
        <div v-for="(payment, index) in paymentList" :key="index">
            <p class="payment-date-group" v-if="index == 0 || paymentList[index - 1].groupDate != payment.groupDate">{{
                payment.groupDate }}</p>
            <div class="user-payment-card"
                :class="this.$route.params.fbid == payment.toAccount ? 'from-user-payment-card' : 'to-user-payment-card'">
                <div class="payment-card" @click="currentPaymentDetail = payment; isPaymentDetailModalOpen = true;">
                    <h4>{{ payment.paymentTitle }}</h4>
                    <h1>{{ payment.amount }}</h1>
                    <small>{{ payment.note }}</small>
                    <div class="payment-date-and-info">
                        <small v-if="payment.status == 'pending'" class="payment-pending-status"><i
                                class="fa-regular fa-clock"></i> {{ payment.statusLabel }}</small>
                        <small v-else-if="payment.status == 'paid'" class="payment-paid-status"><i
                                class="fa-regular fa-circle-check"></i>
                            {{ payment.paidOn }}</small>
                        <small v-else-if="payment.status == 'requested'" class="payment-expired-status"><i
                                class="fa-regular fa-hand"></i> {{ payment.requestedOn }}</small>
                        <small v-else-if="payment.status == 'expired'" class="payment-expired-status"><i
                                class="fa-regular fa-x"></i> {{ payment.expiredOn }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div v-else class="empty-state">
        <img :src="asset('images.noPaymentEmptyState')" alt="">
        <h2>No Transaction Found</h2>
    </div>

    <div v-if="toUserDetail" class="payment-options">
        <h2><img :src="toUserDetail.avatar" alt=""> {{ toUserDetail.name }}</h2>

        <div v-if="!toUserDetail.isDeleted">
            <button class="add-contact-btn" v-if="!toUserDetail.isContact" @click="addNewContact">Add to
                contact</button>
            <button class="pay-btn" @click="this.modalType = 'pay'; this.isModalOpen = true">Pay <i
                    class="fa-solid fa-money-bill-transfer"></i></button>
            <button class="request-btn" @click="this.modalType = 'request'; this.isModalOpen = true">Request <i
                    class="fa-solid fa-notes-medical"></i></button>
        </div>
    </div>

    <span v-if="toUserDetail && toUserDetail.isDeleted" style="color: red;margin-left: 1rem;"> Account has been deleted !</span>
</template>
<script>
import PageLoader from '../layout/PageLoader.vue';
import PartLoader from '../layout/PartLoader.vue';
import ScrollToBottomButton from '../layout/ScrollToBottomButton.vue';
import PayOrRequestFromContact from '../modals/PayOrRequestFromContact.vue';
import PaymentDetail from '../modals/PaymentDetail.vue';
import { toast } from 'vue3-toastify';

export default {
    components: {
        PageLoader,
        PartLoader,
        ScrollToBottomButton,
        PayOrRequestFromContact,
        PaymentDetail
    },
    data() {
        return {
            isPageLoading: true,
            isPaymentLoading: false,
            currentPage: 1,
            perPage: 10,
            lastPage: 1,
            search: '',
            paymentList: [],
            isModalOpen: false,
            modalType: 'pay',
            isRequestModalOpen: false,
            isPaymentDetailModalOpen: false,
            currentPaymentDetail: null,
            toUserDetail: null
        }
    },
    methods: {
        async getToUserDetail() {
            try {
                if (!this.isApiCalled) {
                    this.isApiCalled = true;

                    let url = this.api('payment.toUserDetail', { toUserFbid: this.$route.params.fbid });

                    await this.call.get(url).then(response => (
                        response.data.data ?
                            (this.toUserDetail = response.data.data,
                                this.isApiCalled = false) : this.$router.push({ name: 'contact' })
                    ));
                }
            } catch (e) {
                this.isApiCalled = false;
            }
        },
        async getPaymentList(isInitialLoad = false) {
            try {
                if (isInitialLoad) {
                    this.isPageLoading = true;
                    this.currentPage = 1;
                    this.lastPage = 1;
                }

                if (!this.isApiCalled && this.currentPage <= this.lastPage) {
                    this.isApiCalled = true;

                    if (!isInitialLoad) {
                        this.isPaymentLoading = true;
                    }

                    let url = this.api('payment.list', { toUserFbid: this.$route.params.fbid, currentPage: this.currentPage, perPage: this.perPage });

                    url = this.search ? url + '/' + this.search : url;

                    await this.call.get(url).then(response => (
                        this.paymentList = this.paymentList.length > 0 && !isInitialLoad ? [...response.data.data.reverse(), ...this.paymentList] : response.data.data.reverse(),
                        this.lastPage = response.data.meta.lastPage,
                        this.currentPage++,
                        this.isApiCalled = false,
                        isInitialLoad ? this.isPageLoading = false : this.isPaymentLoading = false
                    ));
                }
            } catch (e) {
                this.isApiCalled = false;
                isInitialLoad ? this.isPageLoading = false : this.isPaymentLoading = false;
            }
        },
        async addNewContact() {
            try {
                if (!this.isApiCalled) {
                    this.isApiCalled = true;
                    this.isPageLoading = true;

                    let url = this.api('contact.addNew');

                    url = url + '/' + this.toUserDetail.fbid;

                    await this.call.get(url).then(response => (
                        toast.success(response.data.message),
                        this.toUserDetail.isContact = true,
                        this.isApiCalled = false,
                        this.isPageLoading = false
                    ));
                }
            } catch (e) {
                this.isApiCalled = false;
                this.isPageLoading = false
            }
        }
    },
    async created() {
        await this.getToUserDetail();
        await this.getPaymentList(true);
        window.scrollTo(0, document.body.scrollHeight);
    },
    mounted() {
        let lastScrollTop = window.scrollY;

        document.addEventListener('scroll', () => {
            const backBtnArea = document.getElementsByClassName('back-btn')[0];
            const rect = backBtnArea ? backBtnArea.getBoundingClientRect() : null;
            let currentScrollTop = window.scrollY ?? 0;

            if (rect && rect.top >= 0 && currentScrollTop < lastScrollTop) {
                this.getPaymentList()

            }
            lastScrollTop = currentScrollTop;
        });
    }
}
</script>
