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
    justify-content: space-between;
}

.close-btn {
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

.modal-body{
    display: flex;
    flex-direction: column;
    align-items: center;
}
</style>
<template>
    <div v-if="isVisible" class="modal-container">
        <div class="modal">
            <div class="modal-header">
                <slot name="modalTitle"></slot>
                <button class="close-btn" @click="closeModal"><i class="fa-solid fa-x"></i></button>
            </div>
           <div class="modal-body">
            <slot name="modalBody"></slot>
           </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        show: {
            type: Boolean,
            required: true,
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
            this.$parent.$parent.isModalOpen = false;
        },
    },
};
</script>
