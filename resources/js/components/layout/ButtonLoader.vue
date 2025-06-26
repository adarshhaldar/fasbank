<style scoped>
.btn-loader-container {
    margin-top: 1rem;
    display: flex;
    justify-content: center;
    align-items: center;
}

.btn-loader {
    width: 6rem;
    height: 1rem;
    border-radius: 20px;
    color: rgb(19, 185, 94);
    border: 2px solid white;
    position: relative;
}

.btn-loader::before {
    content: "";
    position: absolute;
    margin: 2px;
    inset: 0 100% 0 0;
    border-radius: inherit;
    background: currentColor;
    animation: l6 1s infinite;
}

@keyframes l6 {
    100% {
        inset: 0
    }
}
</style>
<template>
    <div class="btn-loader-container">
        <div class="btn-loader"></div>
    </div>
</template>
