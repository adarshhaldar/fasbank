<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>{{ config('custom.app_name') }}</title>

     <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/logo.svg') }}"> 

    <!--Fontawesome icons-->
    <script src="https://kit.fontawesome.com/7e2c349626.js"></script>

    <!--Vite resource-->
    @vite(['resources/js/app.js', 'resources/css/app.css'])

    <!--Set token-->
    @if (Session::has('token'))
        <script>
            localStorage.setItem('token', "{{ Session::get('token') }}");
        </script>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="dark-mode">
    <div id="app"></div>
</body>
<script>
    @if (Session::has('error'))
        alert("{{ Session::get('error') }}");
    @endif

    if (Notification.permission !== 'granted') {
        Notification.requestPermission().then(permission => {
            if (permission === 'granted') {
                console.log('Notifications enabled!');
            }
        });
    }
</script>

</html>
