<template>
    <PageLoader v-if="isPageLoading"></PageLoader>
    <Warning :show="isModalOpen" :modalWarningText="modalWarningText" :moduleFunction="moduleFunction">
    </Warning>

    <h2>Account</h2>

    <div v-if="detail" class="profile-card">
        <h3>Information</h3>

        <div class="detail">
            <div class="profile-img">
                <img :src="avatar" alt="avatar">
            </div>

            <div class="profile-details">
                <p><strong>Name:</strong> {{ detail.name }}</p>
                <p><strong>Email:</strong> {{ detail.email }}</p>
                <p><strong>FasBank ID:</strong> {{ detail.bank.fbid }} <small style="cursor:pointer"
                        @click="copyFbidToClipBoard"><i class="fa-regular fa-copy"></i></small></p>
            </div>
        </div>
    </div>

    <div class="profile-card">
        <h3>Actions</h3>
        <div class="profile-details">
            <div class="btn-div">
                <button class="log-out-btn" @click="logout">Log out</button>
            </div>
            <div class="btn-div">
                <button class="log-out-all-btn" @click="logoutAll">Log out from all device</button>
            </div>
            <div class="btn-div">
                <button class="delete-account-btn" @click="deleteAccount">Delete Account</button>
            </div>
        </div>
    </div>
</template>
<script>
import PageLoader from './layout/PageLoader.vue';
import Warning from './modals/Warning.vue';
export default {
    components: {
        PageLoader,
        Warning
    },
    setup() {
        window.scrollTo(0, 0);
    },
    data() {
        return {
            isPageLoading: true,
            isModalOpen: false,
            modalWarningText: 'Are you sure, you want to log out?',
            moduleFunction: 'logOut',
            detail: this.userState.getUser(),
            avatar: null
        }
    },
    methods: {
        async copyFbidToClipBoard() {
            try {
                let id = this.detail.bank.fbid;
                await navigator.clipboard.writeText(id);
                toast.success('FasBank Id copied to clipboard');
            } catch (err) {
                toast.error('Failed to copy text');
            }
        },
        async getDetail() {
            try {
                if (!this.isApiCalled) {
                    this.isApiCalled = true;
                    await this.call.get(this.api('account.detail')).then(response => (
                        this.detail = response.data.data,
                        this.avatar = this.detail.avatar ?? this.asset('images.defaultUserAvatar'),
                        this.userState.setUser(response.data.data),
                        this.isPageLoading = false,
                        this.isApiCalled = false
                    ));
                }
            } catch (e) {
                this.isPageLoading = false;
                this.isApiCalled = false;
            }
        },
        logout() {
            this.modalWarningText = 'Are you sure, you want to log out?';
            this.moduleFunction = 'logOut';
            this.isModalOpen = true;
        },
        logoutAll() {
            this.modalWarningText = 'Are you sure, you want to log out of all devices?';
            this.moduleFunction = 'logOutAll';
            this.isModalOpen = true;
        },
        deleteAccount() {
            this.modalWarningText = 'Are you sure, you want to delete your account?';
            this.moduleFunction = 'deleteAccount';
            this.isModalOpen = true;
        }
    },
    async created() {
        await this.getDetail();
    }
}
</script>
