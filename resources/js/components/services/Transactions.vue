<template>
    <ScrollToTopButton></ScrollToTopButton>
    <PageLoader v-if="isPageLoading"></PageLoader>
    <TransactionDetail :show="isTransactionDetailModalOpen" :transaction="currentTransaction"></TransactionDetail>

    <router-link :to="{ name: 'services' }" class="back-btn"><i class="fa-solid fa-arrow-left"></i></router-link>
    <br>
    <br>
    <hr>
    <h2 class="title" style="margin-top: 1rem;">Transactions</h2>

    <div class="transaction-filter">
        <div class="filter-tab">
            <label for="date">Filter by Date: </label>
            <select id="date" v-model.trim="date">
                <option value="">Select Date</option>
                <option value="today">Today</option>
                <option value="yesterday">Yesterday</option>
                <option value="this_week">This Week</option>
                <option value="last_week">Last Week</option>
                <option value="this_month">This Month</option>
                <option value="last_month">Last Month</option>
                <option value="this_year">This Year</option>
                <option value="last_year">Last Year</option>
                <option value="custom_date">Custom</option>
                <option value="range_date">Range</option>
            </select>
            <div v-if="date == 'custom_date'" class="filter-tab custom-date">
                <label for="custom_date">Custom Date</label>
                <input type="date" id="custom_date" v-model.trim="customDate">
            </div>
            <div v-if="date == 'range_date'" class="filter-tab custom-date">
                <label for="range_date">From Date</label>
                <input type="date" id="range_date" v-model.trim="fromDate">
                <label for="range_date">To Date</label>
                <input type="date" id="range_date" v-model.trim="toDate">
            </div>
        </div>
        <div class="filter-tab">
            <label for="transaction">Search by Transaction ID: </label>
            <input type="transaction" id="transaction" placeholder="Search Transaction ID" v-model.trim="transactionId">
        </div>
        <div class="filter-tab">
            <label for="fbid">Search by FB ID: </label>
            <input type="text" id="fbid" placeholder="Search FB ID" v-model.trim="fbId">
        </div>
        <div class="filter-tab-btn">
            <button @click="applyFilter">Apply</button>
            <button @click="clearFilter">Clear</button>
        </div>
    </div>

    <div v-if="transactionList.length > 0" class="service-list">
        <div class="service-card" v-for="(transaction, index) in transactionList" :key="index"
            @click="currentTransaction = transaction; isTransactionDetailModalOpen = true;">
            <h5>#{{ transaction.transactionId }}
                <span style="float: inline-end;">
                    <i class="fa-regular" :class="transaction.status == 'paid' ? 'fa-circle-check' : 'fa-clock'"></i>
                    {{ transaction.statusLabel }}
                </span>
            </h5>
            <h3 class="transaction-title">{{ transaction.paymentTitle }}</h3>
            <h2 :class="transaction.actionClass">
                {{ transaction.amountTitle }}
            </h2>
            <small>{{ transaction.dateTitle }}</small>
        </div>
    </div>
    <div v-else class="empty-state">
        <img :src="asset('images.noTransactionEmptyState')" alt="">
        <h2>No Transaction Found</h2>
    </div>
    <PartLoader v-if="isTransactionLoading"></PartLoader>
</template>
<script>
import { toast } from 'vue3-toastify';
import PageLoader from '../layout/PageLoader.vue';
import PartLoader from '../layout/PartLoader.vue';
import TransactionDetail from '../modals/TransactionDetail.vue';
import DOMPurify from "dompurify";
import ScrollToTopButton from '../layout/ScrollToTopButton.vue';
export default {
    components: {
        PageLoader,
        PartLoader,
        TransactionDetail,
        ScrollToTopButton
    },
    setup() {
        window.scrollTo(0, 0);
    },
    data() {
        return {
            date: '',
            customDate: '',
            fromDate: '',
            toDate: '',
            transactionId: '',
            fbId: '',
            isPageLoading: true,
            isTransactionLoading: false,
            currentTransaction: {},
            currentPage: 1,
            perPage: 6,
            lastPage: 1,
            transactionList: [],
            isTransactionDetailModalOpen: false
        }
    }, methods: {
        clearFilter() {
            if (!this.date && !this.customDate && !this.fromDate && !this.toDate && !this.transactionId && !this.fbid) {
                return true;
            } else {
                this.currentPage = 1;
                this.lastPage = 1;
                this.transactionList = [];

                this.date = this.customDate = this.fromDate = this.toDate = this.transactionId = this.fbId = '';

                this.isPageLoading = true;
                this.getTransactionList(true);
            }
        },
        getFilters() {
            return {
                date: this.date ? DOMPurify.sanitize(this.date) : null,
                customDate: this.date == 'custom_date' ? DOMPurify.sanitize(this.customDate) : null,
                fromDate: this.date == 'range_date' ? DOMPurify.sanitize(this.fromDate) : null,
                toDate: this.date == 'range_date' ? DOMPurify.sanitize(this.toDate) : null,
                transactionId: this.transactionId ? DOMPurify.sanitize(this.transactionId) : null,
                fbId: this.fbId ? DOMPurify.sanitize(this.fbId) : null
            };
        },
        async applyFilter() {
            if (this.date == 'custom_date' && !this.customDate) {
                toast.error('Custom Date is required');
                return;
            }
            if (this.date == 'range_date' && !this.fromDate) {
                toast.error('From Date is required');
                return;
            }
            if (this.date == 'range_date' && !this.toDate) {
                toast.error('To Date is required');
                return;
            }

            this.currentPage = 1;
            this.lastPage = 1;
            this.transactionList = [];

            this.isPageLoading = true;
            await this.getTransactionList(true)
        },
        async getTransactionList(isInitialLoad = false) {
            try {
                if (!this.isApiCalled && this.currentPage <= this.lastPage) {
                    this.isApiCalled = true;

                    if (!isInitialLoad) {
                        this.isTransactionLoading = true;
                    }

                    let url = this.api('transactions.list', { currentPage: this.currentPage, perPage: this.perPage });

                    await this.call.post(url, this.getFilters()).then(response => (
                        this.transactionList = this.transactionList.length > 0 ? [...this.transactionList, ...response.data.data] : response.data.data,
                        this.lastPage = response.data.meta.lastPage,
                        this.currentPage++,
                        this.isApiCalled = false,
                        isInitialLoad ? this.isPageLoading = false : this.isTransactionLoading = false
                    ));
                }
            } catch (e) {
                this.isApiCalled = false;
                this.currentPage++;
                isInitialLoad ? this.isPageLoading = false : this.isTransactionLoading = false;
            }
        }
    },
    async created() {
        await this.getTransactionList(true);
    },
    mounted() {
        document.addEventListener('scroll', () => {
            const footer = document.getElementsByTagName('footer')[0];
            const footerTop = footer.getBoundingClientRect().top;
            const windowHeight = window.innerHeight;

            if (footerTop <= windowHeight && this.currentPage <= this.lastPage) {
                this.getTransactionList();
            }
        });
    }
}</script>
