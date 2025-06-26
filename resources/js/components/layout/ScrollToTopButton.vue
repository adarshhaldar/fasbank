<style scoped>
#scrollToTop {
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

#scrollToTop:hover {
    box-shadow: inset 0 -3rem 0 0 white;
    color: black;
}
</style>
<template>
    <button id="scrollToTop"><i class="fa-solid fa-angle-up"></i></button>
</template>
<script>
export default {
    mounted() {
        const scrollToTopBtn = document.getElementById("scrollToTop");
        const pageTitle = document.getElementsByClassName('title')[0];

        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    scrollToTopBtn.style.display = "none";
                } else {
                    scrollToTopBtn.style.display = "block";
                }
            });
        }, { threshold: 0.1 });

        observer.observe(pageTitle);

        scrollToTopBtn.addEventListener("click", function () {
            window.scrollTo({
                top: 0,
                behavior: "smooth"
            });
        });
    }
}
</script>
