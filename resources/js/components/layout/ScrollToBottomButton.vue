<style scoped>
#scrollToBottom {
    position: fixed;
    bottom: 5rem;
    right: 20px;
    background-color: black;
    border: 1px solid white;
    color: white;
    padding: 1rem;
    cursor: pointer;
    border-radius: 100%;
    box-shadow: inset 0 0 0 0 white;
    z-index: 999;
    display: none;
}

#scrollToBottom:hover {
    box-shadow: inset 0 3rem 0 0 white;
    color: black;
}
</style>
<template>
    <button id="scrollToBottom"><i class="fa-solid fa-angle-down"></i></button>
</template>
<script>
export default {
    mounted() {
        const scrollToBottomBtn = document.getElementById("scrollToBottom");
        const footer = document.getElementsByTagName("footer")[0];

        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    scrollToBottomBtn.style.display = "none";
                } else {
                    scrollToBottomBtn.style.display = "block";
                }
            });
        }, { threshold: 0.1 });

        observer.observe(footer);

        scrollToBottomBtn.addEventListener("click", function () {
            window.scrollTo({
                top: document.body.scrollHeight,
                behavior: "smooth"
            });
        });
    }
}
</script>
