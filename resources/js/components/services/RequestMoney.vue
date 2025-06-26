<template>
    <router-link :to="{ name: 'services' }" class="back-btn"><i class="fa-solid fa-arrow-left"></i></router-link>
    <br>
    <br>
    <hr>
    <h2 class="title">Request Money</h2>

    <div class="transfer-request-body">
        <form>
            <label for="fbid">FB ID <span class="astrik">*</span></label>
            <input type="text" id="fbid" v-model.trim="fbid" placeholder="Enter FB ID here" @input="checkfbid"
                @keyup.enter="action">

            <label for="amount">Amount <span class="astrik">*</span></label>
            <input type="text" id="amount" v-model.trim="amount" placeholder="Enter amount here" @input="checkAmount"
                @keyup.enter="action">

            <label for="amount">Note <small>(optional)</small></label>
            <input type="text" id="note" v-model.trim="note" placeholder="Enter note here" @input="checkNote"
                @keyup.enter="action">

            <ButtonLoader v-if="payButtonLoading"></ButtonLoader>
            <button v-else class="pay-btn-color" @click="request">Request</button>
        </form>
    </div>
</template>
<script>
import { toast } from "vue3-toastify";
import DOMPurify from "dompurify";
import ButtonLoader from '../layout/ButtonLoader.vue';
export default {
    components: {
        ButtonLoader
    },
    data() {
        return {
            fbid: '',
            amount: '',
            note: '',
            payButtonLoading: false,
        }
    },
    methods: {
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
        async request() {
            try {
                if (!this.isApiCalled) {
                    this.isApiCalled = true;
                    this.payButtonLoading = true;

                    let url = this.api('payment.request');
                    let data = {
                        toUserFbid: DOMPurify.sanitize(this.fbid),
                        amount: this.amount ? DOMPurify.sanitize(this.amount) : null,
                        note: this.note ? DOMPurify.sanitize(this.note) : null,
                    };

                    await this.call.post(url, data).then(response => (
                        toast.success(response.data.message),
                        this.fbid = this.amount = this.note = '',
                        this.isApiCalled = false,
                        this.payButtonLoading = false
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
