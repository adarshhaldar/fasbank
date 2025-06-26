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

.modal-action-yes-btn:hover {
    box-shadow: inset 6rem 0 1px 0 rgb(244, 65, 65);
    border-color: transparent;
}

.modal-action-no-btn:hover {
    box-shadow: inset -6rem 0 1px 0 rgb(19, 185, 94);
    border-color: transparent;
}
</style>
<template>
    <div v-if="isVisible" class="modal-container">
        <div class="modal">
            <div class="modal-body">
                <img :src="asset('images.warningIcon')" alt="">
                <h3>{{ modalWarningText }}</h3>
                <div>
                    <ButtonLoader v-if="yesButtonLoading"></ButtonLoader>
                    <div v-else class="modal-action">
                        <button class="modal-action-yes-btn" @click="callBack">Yes</button>
                        <button class="modal-action-no-btn" @click="closeModal">No</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { toast } from 'vue3-toastify';
import ButtonLoader from '../layout/ButtonLoader.vue';
export default {
    props: {
        show: {
            type: Boolean,
            required: true,
        },
        modalWarningText: {
            type: String,
            required: true
        },
        moduleFunction: {
            type: String,
            required: true
        },
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
            yesButtonLoading: false,
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
            this.$parent.isModalOpen = false;
        },
        async logOutAll() {
            try {
                if (!this.isApiCalled) {
                    this.isApiCalled = true;
                    this.yesButtonLoading = true;

                    await this.call.get(this.api('account.logOutAll')).then(response => (
                        this.closeModal(),
                        localStorage.removeItem('token'),
                        this.$router.push({ name: 'login' }),
                        setTimeout(() => {
                            toast.success(response.data.message)
                        }, 200)
                    ));
                }
            } catch (e) {
                this.isApiCalled = false;
                this.yesButtonLoading = false;
            }
        },
        async logOut() {
            try {
                if (!this.isApiCalled) {
                    this.isApiCalled = true;
                    this.yesButtonLoading = true;

                    await this.call.get(this.api('account.logOut')).then(response => (
                        this.closeModal(),
                        localStorage.removeItem('token'),
                        this.$router.push({ name: 'login' }),
                        setTimeout(() => {
                            toast.success(response.data.message)
                        }, 200)
                    ));
                }
            } catch (e) {
                this.isApiCalled = false;
                this.yesButtonLoading = false;
            }
        },
        async deleteAccount() {
            try {
                if (!this.isApiCalled) {
                    this.isApiCalled = true;
                    this.yesButtonLoading = true;

                    await this.call.get(this.api('account.delete')).then(response => (
                        this.closeModal(),
                        localStorage.removeItem('token'),
                        this.$router.push({ name: 'login' }),
                        setTimeout(() => {
                            toast.success(response.data.message)
                        }, 200)
                    ));
                }
            } catch (e) {
                this.isApiCalled = false;
                this.yesButtonLoading = false;
            }
        },
        async callBack() {
            if (this.moduleFunction == 'logOut') {
                await this.logOut();
            }

            if (this.moduleFunction == 'logOutAll') {
                await this.logOutAll();
            }

            if (this.moduleFunction == 'deleteAccount') {
                await this.deleteAccount();
            }
        }
    },
};
</script>
