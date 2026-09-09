<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kantin - Admin Panel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800 font-sans min-h-screen flex flex-col">
    
    <!-- Topbar -->
    <header class="bg-red-600 text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
            <h1 class="font-bold text-xl">Kantin Teknik - Admin</h1>
            <div class="text-sm">Pengelola Pusat</div>
        </div>
    </header>

    <!-- Konten Utama dengan dukungan overflow untuk tabel -->
    <main class="flex-1 max-w-7xl mx-auto w-full p-4 md:p-6 overflow-x-auto">
        @yield('content')
    </main>

</body>
</html>