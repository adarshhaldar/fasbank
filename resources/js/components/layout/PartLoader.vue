<style scoped>
.loader-container{
    margin-top: 0.5rem;
    display: flex;
    justify-content: center;
    align-items: center;
}

.loader {
    width: 3.5rem;
    aspect-ratio: 1;
    display: grid;
    color: #ffffff;
    background: radial-gradient(farthest-side, currentColor calc(100% - 6px), #0000 calc(100% - 5px) 0);
    -webkit-mask: radial-gradient(farthest-side, #0000 calc(100% - 13px), #000 calc(100% - 12px));
    border-radius: 50%;
    animation: l19 1.5s infinite linear;
}

.loader::before,
.loader::after {
    content: "";
    grid-area: 1/1;
    background:
        linear-gradient(currentColor 0 0) center,
        linear-gradient(currentColor 0 0) center;
    background-size: 100% 10px, 10px 100%;
    background-repeat: no-repeat;
}

.loader::after {
    transform: rotate(45deg);
}

@keyframes l19 {
    100% {
        transform: rotate(1turn)
    }
}
</style>
<template>
    <div class="loader-container">
        <div class="loader"></div>
    </div>
</template>
