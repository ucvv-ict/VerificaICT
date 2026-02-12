<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VerificaICT - Area Operatore</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

    <header class="bg-white shadow mb-4">
        <div class="max-w-4xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="font-bold text-lg">
                VerificaICT
            </div>

            <div class="text-sm text-gray-600">
                {{ auth()->user()->name }}
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4">
        @yield('content')
    </main>

</body>
</html>
