<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }}</title>

    @filamentStyles
    @vite(['resources/css/filament/admin/theme.css'])
</head>

<body class="min-h-screen bg-gray-50 dark:bg-gray-950 antialiased">

    {{ $slot }}

    @filamentScripts
</body>
</html>
