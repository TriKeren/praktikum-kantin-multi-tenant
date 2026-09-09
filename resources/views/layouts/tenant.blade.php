<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kantin - Tenant Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900 font-sans flex min-h-screen">
    
    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r border-gray-200 hidden md:block">
        <div class="h-16 flex items-center px-6 border-b border-gray-200 font-bold text-lg">
            Tenant Portal
        </div>
        <nav class="p-4 space-y-2">
            <a href="#" class="block px-4 py-2 rounded bg-red-50 text-red-600 font-medium">Dashboard</a>
            <a href="#" class="block px-4 py-2 rounded text-gray-600 hover:bg-gray-50">Pesanan</a>
        </nav>
    </aside>

    <!-- Konten Utama -->
    <div class="flex-1 flex flex-col">
        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>

</body>
</html>