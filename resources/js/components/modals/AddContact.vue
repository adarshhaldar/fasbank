<style scoped>
.add-contact {
    padding: 1rem;
}

.add-contact-list {
    flex-wrap: wrap;
    display: flex;
    align-items: center;
    justify-content: center;
}

.add-contact-card {
    background: #333;
    padding: 1rem;
    border-radius: 5px;
    width: 8.5rem;
    color: white;
    text-decoration: none;
    display: flex;
    margin: 0rem 0.5rem 0.5rem 0.5rem;
    flex-direction: column;
    align-items: center;
    box-shadow: 0rem 0rem 0px 0 black;
}

.add-contact-img img {
    display: inline-block;
    width: 4rem;
    border-radius: 100%;
}

.add-contact-detail {
    text-align: center;
    padding: 1rem 0rem;
}

.add-contact-detail h5, .add-contact-detail small {
    display: block;
    width: 7.5rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.add-contact-req-btn {
    background-color: rgb(19, 185, 94);
    border: none;
    padding: 0.5rem;
    border-radius: 5px;
    margin-top: 1rem;
    margin-bottom: -1rem;
    color: white;
    box-shadow: inset 0 0 0 0 rgb(255, 255, 255);
}

.add-contact-req-btn:hover {
    box-shadow: inset 4.5rem 0 1px 0 white;
    color: black;
}

.add-contact-added-btn {
    background-color: transparent;
    padding: 0.5rem;
    border: none;
    margin-top: 1rem;
    margin-bottom: -1rem;
    box-shadow: inset 0 0 0 0 rgb(255, 255, 255);
    color: rgb(19, 185, 94);
    cursor: default;
}
</style>
<template>
    <Modal :show="show">
        <template v-slot:modalTitle>
            <h3>Add New Contact</h3>
        </template>
        <template v-slot:modalBody>
            <div class="contact-search">
                <div class="contact-search-tab">
                    <input type="text" placeholder="Search By FBID" @input="handleSearchInput" v-model="search">
                    <button v-if="search" @click="resetVariables"><i class="fa-solid fa-x"></i></button>
                </div>
            </div>
            <div v-if="contact" class="add-contact">
                <div class="add-contact-list">
                    <div class="add-contact-card">
                        <div class="add-contact-img">
                            <img :src="contact.avatar" alt="">
                        </div>

                        <div class="add-contact-detail">
                            <h5>{{ contact.name }}</h5>
                            <small>{{ contact.fbid }}</small>
                            <small v-if="contact.isContact" class="add-contact-added-btn" disabled>Added <i
                                    class="fa-solid fa-check"></i></small>
                            <ButtonLoader v-if="isButtonLoading"></ButtonLoader>
                            <button v-else-if="!isButtonLoading && !contact.isContact" class="add-contact-req-btn" @click="addNewContact">Add Now</button>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else-if="!contact && !isContactLoading && !isInitialCall" class="empty-state">
                <img :src="asset('images.noSearchEmptyState')" alt="">
                <h2>No Account Found</h2>
            </div>
            <div v-if="isInitialCall" class="empty-state">
                <img :src="asset('images.searchPersonEmptyState')" alt="">
                <h2>Search For Account</h2>
            </div>
            <PartLoader v-if="isContactLoading"></PartLoader>
        </template>
    </Modal>
</template>
<script>
import { toast } from 'vue3-toastify';
import PartLoader from '../layout/PartLoader.vue';
import ButtonLoader from '../layout/ButtonLoader.vue';
import Modal from './Modal.vue';
import { debounce } from 'lodash';
import DOMPurify from "dompurify";
export default {
    components: {
        PartLoader,
        ButtonLoader,
        Modal
    },
    props: {
        show: {
            type: Boolean,
            required: true,
        },
    },
    data() {
        return {
            isInitialCall: true,
            isContactLoading: false,
            isButtonLoading: false,
            search: '',
            contact: null,
        }
    },
    computed: {
        isVisible() {
            return this.show;
        },
    },
    watch: {
        isVisible(newValue) {
            if (newValue) {
                this.resetVariables();
            } else {
                this.search = '';
                this.contact = null;
            }
        },
    },
    methods: {
        resetVariables(noApiCall = true) {
            this.contact = null;
            this.isInitialCall = true;
            if (noApiCall) {
                this.search = '';
            }
        },
        debouncedSearch: debounce(function () {
            this.searchContact();
        }, 500),
        async searchContact() {
            this.resetVariables(false);
            await this.getContact();
        },
        handleSearchInput() {
            if (this.search && /^\d+$/.test(this.search)) {
                this.debouncedSearch();
            } else {
                this.resetVariables();
            }
        },
        async getContact() {
            try {
                if (!this.isApiCalled) {
                    this.isInitialCall = false;
                    this.isApiCalled = true;

                    this.isContactLoading = true;

                    let url = this.api('contact.findNew');

                    url = this.search ? url + '/' + DOMPurify.sanitize(this.search) : url;

                    await this.call.get(url).then(response => (
                        this.contact = response.data.data,
                        this.isApiCalled = false,
                        this.isContactLoading = false
                    ));
                }
            } catch (e) {
                this.isApiCalled = false;
                this.isContactLoading = false;
            }
        },
        async addNewContact(){
            try {
                if (!this.isApiCalled) {
                    this.isApiCalled = true;
                    this.isButtonLoading = true;

                    let url = this.api('contact.addNew');

                    url = this.search ? url + '/' + this.search : url;

                    await this.call.get(url).then(response => (
                        toast.success(response.data.message),
                        this.contact.isContact = true,
                        this.isApiCalled = false,
                        this.isButtonLoading = false,
                        this.$parent.reFetchListAfterAddingNewContact(),
                        this.$parent.isModalOpen = false
                    ));
                }
            } catch (e) {
                this.isApiCalled = false;
                this.isButtonLoading = false;
            }
        }
    },
};
</script>
