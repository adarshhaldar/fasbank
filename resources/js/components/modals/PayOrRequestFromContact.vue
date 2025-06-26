<style scoped>
h3 {
    white-space: nowrap;
    overflow: hidden;
    width: 100%;
    text-overflow: ellipsis;
}

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
.modal-action-yes-btn,
.modal-action-no-btn {
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
    width: 8rem;
}

.modal-action {
    margin-top: 2rem;
    display: flex;
    justify-content: space-evenly;
}

.modal-action-yes-btn,
.modal-action-no-btn {
    padding: 0.8rem;
    width: 6rem;
    cursor: pointer;
}

.pay-btn-color:hover {
    box-shadow: inset 6rem 0 1px 0 rgb(244, 65, 65);
    border-color: transparent;
}

.pay-cancel-color:hover {
    box-shadow: inset -6rem 0 1px 0 rgb(19, 185, 94);
    border-color: transparent;
}

.request-btn-color:hover {
    box-shadow: inset 6rem 0 1px 0 rgb(19, 185, 94);
    border-color: transparent;
}

.request-cancel-color:hover {
    box-shadow: inset -6rem 0 1px 0 rgb(244, 65, 65);
    border-color: transparent;
}

form {
    padding: 1rem;
    display: flex;
    flex-direction: column;
    text-align: start;
}

form label {
    padding: 0.5rem;
}

form input {
    background-color: transparent;
    border: 1px solid white;
    padding: 1rem 0.5rem;
    color: white;
    border-radius: 5px;
}
</style>
<template>
    <div v-if="isVisible" class="modal-container">
        <div class="modal">
            <div class="modal-body">
                <img :src="modalType == 'pay' ? asset('images.moneyPay') : asset('images.moneyRequest')" alt="">
                <h3>{{ modalType == 'pay' ? 'Payment to ' + toUserName : 'Request to ' + toUserName }}</h3>
                <small>FBID: {{ fbid }}</small>
                <form>
                    <label for="amount">Amount <span class="astrik">*</span></label>
                    <input type="text" id="amount" v-model.trim="amount" placeholder="Enter amount here"
                        @input="checkAmount" @keyup.enter="action">

                    <label for="amount">Note <small>(optional)</small></label>
                    <input type="text" id="note" v-model.trim="note" placeholder="Enter note here" @input="checkNote"
                        @keyup.enter="action">
                </form>
                <div>
                    <ButtonLoader v-if="payButtonLoading"></ButtonLoader>
                    <div v-else class="modal-action">
                        <button class="modal-action-yes-btn"
                            :class="modalType == 'pay' ? 'pay-btn-color' : 'request-btn-color'" @click="action">{{
                                modalType == 'pay' ? 'Pay' : 'Request' }}</button>
                        <button class="modal-action-no-btn"
                            :class="modalType == 'pay' ? 'pay-cancel-color' : 'request-cancel-color'"
                            @click="closeModal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { toast } from "vue3-toastify";
import DOMPurify from "dompurify";
import ButtonLoader from '../layout/ButtonLoader.vue';
export default {
    props: {
        show: {
            type: Boolean,
            required: true,
        },
        modalType: {
            type: String,
            required: true,
        },
        toUserName: {
            type: String,
            required: true
        },
        fbid: {
            type: String,
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
            amount: '',
            note: '',
            payButtonLoading: false,
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
            this.amount = '';
            this.note = '';
            this.$parent.isModalOpen = false;
        },
        checkAmount() {
            if (this.amount && (!/^\d+$/.test(this.amount) || this.amount <= 0)) {
                this.amount = ''
            }

            if (this.amount.length > 12) {
                this.amount = this.amount.slice(0, 12)
            }
        },
        checkNote() {
            if (this.note.length > 30) {
                this.note = this.note.slice(0, 30)
            }
        },
        async action() {
            try {
                if (!this.isApiCalled) {
                    this.isApiCalled = true;
                    this.payButtonLoading = true;

                    let url = this.modalType == 'pay' ? this.api('payment.pay') : this.api('payment.request');
                    let data = {
                        toUserFbid: DOMPurify.sanitize(this.fbid),
                        amount: this.amount ? DOMPurify.sanitize(this.amount) : null,
                        note: this.note ? DOMPurify.sanitize(this.note) : null,
                    };

                    await this.call.post(url, data).then(response => (
                        toast.success(response.data.message),
                        this.isApiCalled = false,
                        this.payButtonLoading = false,
                        this.$parent.getPaymentList(true),
                        this.closeModal()
                    ));
                }
            } catch (e) {
                this.isApiCalled = false;
                this.payButtonLoading = false;
            }
        }
    },
};
</script>
