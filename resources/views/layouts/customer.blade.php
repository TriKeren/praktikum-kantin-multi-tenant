<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kantin - Customer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-900 text-gray-100 antialiased">
    <!-- Pembatas lebar layar (Mobile-first) -->
    <div class="max-w-md mx-auto min-h-screen bg-gray-950 shadow-2xl flex flex-col">
        <main class="flex-grow p-4">
            @yield('content')
        </main>
    </div>
</body>
</html>