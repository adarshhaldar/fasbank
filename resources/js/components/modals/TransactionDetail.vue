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

.account-detail {
    margin: 1rem 0rem;
    width: 14rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.account-detail div{
    width: 8rem;
}

.account-detail h4{
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.account-detail small{
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.account-detail i {
    cursor: pointer;
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
    width: 100%;
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
                <h5>From</h5>
                <div class="account-detail">
                    <img :src="transaction.fromAccountDetail.avatar" alt="">
                    <div>
                        <h4>{{ userState.getUser().id == transaction.fromAccountDetail.id ? 'You' :
                            transaction.fromAccountDetail.name }}</h4>
                        <small>{{ transaction.fromAccount }} <i class="fa-regular fa-copy"
                                @click="copyFromAccountFbId"></i></small>
                    </div>
                </div>

                <h5>To</h5>
                <div class="account-detail">
                    <div>
                        <h4>{{ userState.getUser().id == transaction.toAccountDetail.id ? 'You' :
                            transaction.toAccountDetail.name }}</h4>
                        <small>{{ transaction.toAccount }} <i class="fa-regular fa-copy"
                                @click="copyToAccountFbId"></i></small>
                    </div>
                    <img :src="transaction.toAccountDetail.avatar" alt="">
                </div>

                <div class="payment-details">
                    <h5>Transaction ID: {{ transaction.transactionId }} <i class="fa-regular fa-copy"
                            @click="copyTransactionId"></i></h5>
                    <h1>{{ transaction.amount }}</h1>
                    <small v-if="transaction.status == 'pending'" class="transaction-pending-status"><i
                            class="fa-regular fa-clock"></i>
                        {{ transaction.statusLabel }}</small>
                    <small v-if="transaction.status == 'paid'" class="transaction-paid-status"><i
                            class="fa-regular fa-circle-check"></i>
                        {{ transaction.statusLabel }}</small>
                    <small>{{ transaction.note }}</small>
                    <small>{{ transaction.createdAt }}</small>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { toast } from 'vue3-toastify';
export default {
    props: {
        show: {
            type: Boolean,
            required: true,
        },
        transaction: {
            type: Object,
            required: true
        },
    },
    computed: {
        isVisible() {
            return this.show;
        },
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
            this.$parent.isTransactionDetailModalOpen = false;
        },
        async copyTransactionId() {
            try {
                let id = this.transaction.transactionId;
                await navigator.clipboard.writeText(id);
                toast.success('Transaction Id copied to clipboard');
            } catch (err) {
                toast.error('Failed to copy text');
            }
        },
        async copyFromAccountFbId() {
            try {
                let id = this.transaction.fromAccount;
                await navigator.clipboard.writeText(id);
                toast.success('FasBank Id copied to clipboard');
            } catch (err) {
                toast.error('Failed to copy text');
            }
        },
        async copyToAccountFbId() {
            try {
                let id = this.transaction.toAccount;
                await navigator.clipboard.writeText(id);
                toast.success('FasBank Id copied to clipboard');
            } catch (err) {
                toast.error('Failed to copy text');
            }
        },
    },
};
</script>
