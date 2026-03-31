<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Tiara Smart Assistant</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/LoginApp.jsx'])
</head>
<body class="h-full antialiased m-0 p-0">
    
    {{-- Pass errors and old input to React --}}
    <script>
        window.LaravelData = {
            errors: @json($errors->all()),
            oldInput: @json(old())
        };
    </script>

    <div id="react-login-root" class="min-h-screen w-full"></div>

</body>
</html>
