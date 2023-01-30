<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>GeoJSON Helper</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Styles -->
    @mapstyles
    <livewire:styles />

    <!-- Scripts -->
    @mapscripts
    <wireui:scripts />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased">
    <x-notifications z-index="z-50" blur="md" align="center" />
    <div
        class="flex min-h-screen flex-col items-center bg-gray-100 pt-2 dark:bg-gray-900 sm:justify-center sm:pt-6 sm:pt-0">
        <div
            class="my-2 w-full max-w-screen-2xl bg-white px-1 py-4 shadow-md dark:bg-gray-800 sm:my-6 sm:rounded-lg sm:px-6">
            {{ $slot }}
        </div>
    </div>
    <livewire:scripts />
</body>

</html>
