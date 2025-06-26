<style scoped>
.modal-container {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 9999;
    width: 100vw;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: rgba(0, 0, 0);
}

.modal {
    width: 20rem;
    padding: 1rem;
    border: 1px solid white;
    border-radius: 5px;
}

.modal-header {
    display: flex;
    justify-content: end;
}

.close-btn,
.modal-action-pay-btn {
    padding: 0.5rem;
    border-radius: 5px;
    border: 1px solid white;
    text-decoration: none;
    color: white;
    background-color: transparent;
    transition: 0.3s all;
    box-shadow: inset 0 0 0 0 black;
}

.close-btn:hover {
    box-shadow: inset 1.7rem 0 1px 0 white;
    color: black;
}

.modal-body {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.modal-body div {
    width: 100%;
}

.modal-body img {
    width: 4rem;
    border-radius: 100%;
    margin-bottom: 0.5rem;
}

.modal-action {
    margin-top: 2rem;
    display: flex;
    justify-content: space-evenly;
}

.modal-action-pay-btn {
    padding: 0.8rem;
    width: 6rem;
    cursor: pointer;
    margin-top: 1rem;
}

.modal-action-pay-btn:hover {
    box-shadow: inset 6rem 0 1px 0 rgb(244, 65, 65);
    border-color: transparent;
}

.payment-details {
    display: flex;
    flex-direction: column;
    padding: 1rem 0rem;
    border: 1px solid white;
    border-radius: 10px;
    margin-top: 1rem;
}

.payment-details h5 {
    width: 100%;
    text-align: start;
    padding: 0.5rem;
}

.payment-details h5 i {
    cursor: pointer;
}
</style>
<template>
    <div v-if="isVisible" class="modal-container">
        <div class="modal">
            <div class="modal-header">
                <button class="close-btn" @click="closeModal"><i class="fa-regular fa-x"></i></button>
            </div>
            <div class="modal-body">
                <img :src="toUserDetail.avatar" alt="">
                <h4>{{ payment.paymentTitle }}</h4>
                <h1>{{ payment.amount }}</h1>
                <small v-if="payment.status == 'pending'" class="payment-pending-status"><i
                        class="fa-regular fa-clock"></i>
                    {{ payment.statusLabel }}</small>
                <small v-else-if="payment.status == 'paid'" class="payment-paid-status"><i
                        class="fa-regular fa-circle-check"></i>
                    {{ payment.paidOn }}</small>
                <small v-else-if="payment.status == 'requested'" class="payment-expired-status"><i
                        class="fa-regular fa-hand"></i> {{ payment.requestedOn }}</small>
                <small v-else-if="payment.status == 'expired'" class="payment-expired-status"><i
                        class="fa-regular fa-x"></i> {{ payment.expiredOn }}</small>

                <div class="payment-details">
                    <h5>Transaction ID: {{ payment.transactionId }} <i class="fa-regular fa-copy"
                            @click="copyTransactionId"></i></h5>
                    <h5>From Account: {{ payment.fromAccount }} <i class="fa-regular fa-copy"
                            @click="copyFromAccountFbId"></i></h5>
                    <h5>To Account: {{ payment.toAccount }} <i class="fa-regular fa-copy"
                            @click="copyToAccountFbId"></i></h5>
                </div>
                <div>
                    <ButtonLoader
                        v-if="isPayButtonLoading && this.$route.params.fbid != payment.toAccount && payment.status == 'requested'">
                    </ButtonLoader>
                    <button v-else-if="!toUserDetail.isDeleted && payment.status == 'requested' && this.$route.params.fbid != payment.toAccount" class="modal-action-pay-btn"
                        @click="pay">Pay</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { toast } from 'vue3-toastify';
import ButtonLoader from '../layout/ButtonLoader.vue';
import DOMPurify from "dompurify";
export default {
    props: {
        show: {
            type: Boolean,
            required: true,
        },
        payment: {
            type: Object,
            required: true
        },
        toUserDetail: {
            type: Object,
            required: true
        }
    },
    computed: {
        isVisible() {
            return this.show;
        },
    },
    components: {
        ButtonLoader
    },
    data() {
        return {
            isPayButtonLoading: false,
        }
    },
    watch: {
        isVisible(newValue) {
            if (newValue) {
                document.documentElement.style.overflow = "hidden";
            } else {
                document.documentElement.style.overflow = "";
            }
        },
    },
    methods: {
        closeModal() {
            this.$parent.isPaymentDetailModalOpen = false;
        },
        async copyTransactionId() {
            try {
                let id = this.payment.transactionId;
                await navigator.clipboard.writeText(id);
                toast.success('Transaction Id copied to clipboard');
            } catch (err) {
                toast.error('Failed to copy text');
            }
        },
        async copyFromAccountFbId() {
            try {
                let id = this.payment.fromAccount;
                await navigator.clipboard.writeText(id);
                toast.success('FasBank Id copied to clipboard');
            } catch (err) {
                toast.error('Failed to copy text');
            }
        },
        async copyToAccountFbId() {
            try {
                let id = this.payment.toAccount;
                await navigator.clipboard.writeText(id);
                toast.success('FasBank Id copied to clipboard');
            } catch (err) {
                toast.error('Failed to copy text');
            }
        },
        async pay() {
            try {
                if (!this.isApiCalled) {
                    this.isApiCalled = true;
                    this.isPayButtonLoading = true;

                    let url = this.api('payment.payRequest');
                    let data = {
                        transactionId: DOMPurify.sanitize(this.payment.transactionId),
                        toUserFbid: DOMPurify.sanitize(this.$route.params.fbid)
                    };

                    await this.call.post(url, data).then(response => (
                        toast.success(response.data.message),
                        this.payment = response.data.data,
                        this.isPayButtonLoading = false,
                        this.closeModal()
                    ));
                }
            } catch (e) {
                this.isApiCalled = false;
                this.$parent.getPaymentList(true);
                this.isPayButtonLoading = false;
                this.closeModal();
            }
        },
    },
};
</script>
