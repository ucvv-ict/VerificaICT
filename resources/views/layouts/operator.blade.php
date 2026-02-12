<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VerificaICT - Area Operatore</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

    {{-- HEADER --}}
    <header class="bg-white shadow">
        <div class="max-w-4xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="font-bold text-lg">
                VerificaICT
            </div>

            <div class="text-sm text-gray-600">
                {{ auth()->user()->name }}
            </div>
        </div>
    </header>

    {{-- NAVBAR --}}
    <nav class="bg-gray-50 border-b">
        <div class="max-w-4xl mx-auto px-4 flex gap-6 py-2 text-sm">

            <a href="{{ route('operator.dashboard') }}"
               class="{{ request()->routeIs('operator.dashboard') ? 'font-semibold text-blue-600' : 'text-gray-600 hover:text-blue-600' }}">
                Dashboard
            </a>

            {{-- Quando avrai storico --}}
            <a href="#"
               class="text-gray-400 cursor-not-allowed">
                Storico
            </a>

        </div>
    </nav>

    {{-- CONTENUTO --}}
    <main class="max-w-4xl mx-auto px-4 py-6">
        @yield('content')
    </main>

</body>
</html>
