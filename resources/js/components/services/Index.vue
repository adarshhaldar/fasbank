<template>
    <h2>Services</h2>

    <div class="service-list">
        <router-link :to="{ name: 'recent' }" class="service-card">
            <h3 class="recent-h3"><i class="fa-solid fa-clock"></i> Recent</h3>
            <p>Get your recent recipient with whom you have contacted.</p>
        </router-link>

        <router-link :to="{ name: 'transactions' }" class="service-card">
            <h3 class="transactions-h3"><i class="fa-solid fa-list"></i> Transactions</h3>
            <p>Get all of your transaction, filter out the specific one which you are seeking.</p>
        </router-link>

        <router-link :to="{ name: 'contact' }" class="service-card">
            <h3 class="contact-h3"><i class="fa-solid fa-user-group"></i> Contacts</h3>
            <p>Access your contact, or find a new one to start trading with.</p>
        </router-link>

        <router-link :to="{ name: 'request_money' }" class="service-card">
            <h3 class="request-money-h3"><i class="fa-solid fa-notes-medical"></i> Request Money</h3>
            <p>Request the desired amount of money from a valid account.</p>
        </router-link>

        <router-link :to="{ name: 'transfer_money' }" class="service-card">
            <h3 class="send-money-h3"><i class="fa-solid fa-money-bill-transfer"></i> Transfer Money</h3>
            <p>Transfer the desired amount of money from a valid account.</p>
        </router-link>
    </div>
</template>
<script>
export default {
    setup() {
        window.scrollTo(0, 0);
    }
}
</script>
