<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Styles -->
    @mapstyles
    <livewire:styles/>
    @vite(['resources/css/app.css'])

    <!-- Scripts -->
    @mapscripts
    <wireui:scripts/>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans text-gray-900 antialiased">
<x-notifications z-index="z-50" blur="md" align="center"/>
<div
    class="min-h-screen flex flex-col sm:justify-center items-center pt-2 sm:pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
    <div
        class="w-full max-w-screen-2xl mt-2 sm:mt-6 px-1 sm:px-6 py-4 bg-white dark:bg-gray-800 shadow-md sm:rounded-lg">
        {{ $slot }}
    </div>
</div>
<livewire:scripts/>
</body>
</html>
