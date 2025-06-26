<template>
    <ScrollToTopButton></ScrollToTopButton>
    <PageLoader v-if="isPageLoading"></PageLoader>
    <h2 class="title">Dashboard</h2>
    <div v-if="detail" class="dashboard-row">
        <div class="dashboard-card-pill">
            <h1 style="color: rgb(51, 250, 184);"><i class="fa-solid fa-piggy-bank"></i></h1>
            <p><strong>Balance:</strong> {{ detail.balance }}</p>
        </div>
        <div class="dashboard-card-pill">
            <h1 style="color: rgb(127, 197, 255);"><i class="fa-regular fa-circle-check"></i></h1>
            <p><strong>Transactions Done:</strong> {{ detail.totalPaidTransactions }}</p>
        </div>
        <div class="dashboard-card-pill">
            <h1 style="color: rgb(236, 85, 250);"><i class="fa-solid fa-notes-medical"></i></h1>
            <p><strong>Transactions Received:</strong> {{ detail.totalPaidTransactionsReceived }}</p>
        </div>
        <div class="dashboard-card-pill">
            <h1 style="color: rgb(255, 127, 127);"><i class="fa-solid fa-money-bill-transfer"></i></h1>
            <p><strong>Amount Paid:</strong> {{ detail.totalPaidAmount }}</p>
        </div>
        <div class="dashboard-card-pill">
            <h1 style="color: rgb(19, 185, 94);"><i class="fa-solid fa-money-bill-trend-up"></i></h1>
            <p><strong>Amount Received:</strong> {{ detail.totalPaidAmountReceived }}</p>
        </div>
    </div>

    <div class="chart-tab">
        <h2>Transactions
            <select v-model="transactionFilter" @change="filterTransactionData">
                <option value="today">Today</option>
                <option value="yesterday">Yesterday</option>
                <option value="this_week">This Week</option>
                <option value="last_week">Last Week</option>
                <option value="this_month">This Month</option>
                <option value="last_month">Last Month</option>
                <option value="this_year">This Year</option>
                <option value="last_year">Last Year</option>
            </select>
        </h2>
        <PartLoader v-if="isTransactionDataLoading"></PartLoader>
        <div v-else>
            <BarChart :chart-data="transactionChartData" :chart-options="transactionChartOption"></BarChart>
        </div>

        <br>
        <h2>Amount
            <select v-model="amountFilter" @change="filterAmountData">
                <option value="today">Today</option>
                <option value="yesterday">Yesterday</option>
                <option value="this_week">This Week</option>
                <option value="last_week">Last Week</option>
                <option value="this_month">This Month</option>
                <option value="last_month">Last Month</option>
                <option value="this_year">This Year</option>
                <option value="last_year">Last Year</option>
            </select>
        </h2>
        <PartLoader v-if="isAmountDataLoading"></PartLoader>
        <div v-else>
            <LineChart :chart-data="amountChartData" :chart-options="amountChartOption"></LineChart>
        </div>
    </div>
</template>
<script>
import { toast } from 'vue3-toastify';
import PageLoader from './layout/PageLoader.vue';
import PartLoader from './layout/PartLoader.vue';
import ScrollToTopButton from './layout/ScrollToTopButton.vue';
import LineChart from './charts/LineChart.vue';
import BarChart from './charts/BarChart.vue';
import { reactive } from 'vue';
export default {
    setup() {
        window.scrollTo(0, 0);
    },
    components: {
        PageLoader,
        PartLoader,
        ScrollToTopButton,
        LineChart,
        BarChart
    },
    data() {
        return {
            isPageLoading: true,
            detail: null,
            isTransactionDataLoading: true,
            isAmountDataLoading: true,
            transactionFilter: 'today',
            amountFilter: 'today',
            transactionChartData: reactive({
                labels: [],
                datasets: [{
                    label: '# Of Paid Transactions',
                    data: [],
                    borderWidth: 2,
                    borderColor: '#f57676',
                    pointRadius: 5
                }, {
                    label: '# Of Received Transactions',
                    data: [],
                    borderWidth: 2,
                    borderColor: '#39fa66',
                    pointRadius: 5
                }]
            }),
            transactionChartOption: reactive({
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }),
            amountChartData: reactive({
                labels: [],
                datasets: [{
                    label: 'Amount paid',
                    data: [],
                    borderWidth: 2,
                    borderColor: '#f57676',
                    pointRadius: 5
                }, {
                    label: 'Amount Received',
                    data: [],
                    borderWidth: 2,
                    borderColor: '#39fa66',
                    pointRadius: 5
                }]
            }),
            amountChartOption: reactive({
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            })
        }
    },
    methods: {
        async filterTransactionData() {
            try {
                if (!this.isApiCalled) {
                    this.isApiCalled = true;
                    this.isTransactionDataLoading = true;
                    await this.call.get(this.api('dashboard.transactionDetail', { filter: this.transactionFilter })).then(response => (
                        this.isApiCalled = false,
                        this.isTransactionDataLoading = false,
                        this.transactionChartData.labels = response.data.data?.paidTransaction.xAxis || [],
                        this.transactionChartData.datasets[0].data = response.data.data?.paidTransaction.yAxis || [],
                        this.transactionChartData.datasets[1].data = response.data.data?.receivedTransaction.yAxis || []
                    ));
                }
            } catch (e) {
                this.isApiCalled = false;
                this.isTransactionDataLoading = false;
            }
        },
        async filterAmountData() {
            try {
                if (!this.isApiCalled) {
                    this.isApiCalled = true;
                    this.isAmountDataLoading = true;
                    await this.call.get(this.api('dashboard.amountDetail', { filter: this.amountFilter })).then(response => (
                        this.isApiCalled = false,
                        this.isAmountDataLoading = false,
                        this.amountChartData.labels = response.data.data?.paidAmount.xAxis || [],
                        this.amountChartData.datasets[0].data = response.data.data?.paidAmount.yAxis || [],
                        this.amountChartData.datasets[1].data = response.data.data?.receivedAmount.yAxis || []
                    ));
                }
            } catch (e) {
                this.isApiCalled = false;
                this.isAmountDataLoading = false;
            }
        },
        async getDetail() {
            try {
                if (!this.isApiCalled) {
                    this.isApiCalled = true;
                    await this.call.get(this.api('dashboard.detail')).then(response => (
                        this.detail = response.data.data,
                        this.isPageLoading = false,
                        this.isApiCalled = false,
                        this.transactionChartData.labels = response.data.data.transactionData?.paidTransaction.xAxis || [],
                        this.transactionChartData.datasets[0].data = response.data.data.transactionData?.paidTransaction.yAxis || [],
                        this.transactionChartData.datasets[1].data = response.data.data.transactionData?.receivedTransaction.yAxis || [],
                        this.isTransactionDataLoading = false,
                        this.amountChartData.labels = response.data.data.amountData?.paidAmount.xAxis || [],
                        this.amountChartData.datasets[0].data = response.data.data.amountData?.paidAmount.yAxis || [],
                        this.amountChartData.datasets[1].data = response.data.data.amountData?.receivedAmount.yAxis || [],
                        this.isAmountDataLoading = false
                    ));
                }
            } catch (e) {
                this.isPageLoading = false;
                this.isApiCalled = false;
                this.isTransactionDataLoading = false;
                this.isAmountDataLoading = false;
            }
        }
    },
    async mounted() {
        await this.getDetail();
    },
}
</script>