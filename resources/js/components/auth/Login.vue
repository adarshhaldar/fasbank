<style scoped>
h2 {
    display: block;
    text-align: center;
}

img{
    width: 10rem;
    margin: 0 auto;
    display: block;
}
</style>
<template>
    <h2>Login</h2>
    <img :src="asset('images.loginPageTab')" alt="">
    <a :href="env('GOOGLE_REDIRECT_URL')" class="login-with-google-btn"><i class="fa-brands fa-google fa-bounce"></i> Log in with Google</a>
    <!-- <form action="#" class="auth-form">
        <div class="input-tab">
            <label for="fbid">FBID</label>
            <input type="text" id="fbid" name="fbid" placeholder="Enter your FasBank ID" required>
        </div>

        <div class="input-tab">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
        </div>

        <button type="submit">Login</button>
    </form>
    <p class="auth-link">Don't have an account? <a href="register.html">Register here</a></p> -->
</template>
