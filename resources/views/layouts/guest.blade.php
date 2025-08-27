<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'UndanginAja' }} - {{ config('app.name', 'UndanginAja') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('build/assets/logo.png') }}">
        <link rel="shortcut icon" type="image/png" href="{{ asset('build/assets/logo.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('build/assets/logo.png') }}">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans  text-gray-900 antialiased">
        <div class="min-h-screen bg-pink-100 flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div>
                <a href="/" class="bg-gradient-to-br  to-rose-500 rounded-lg flex items-center justify-center">
                 <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-pink-400 to-rose-500 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-sm">UA</span>
                        </div>
                        <span class="font-script text-2xl font-semibold bg-gradient-to-r from-pink-500 to-rose-500 bg-clip-text text-transparent">
                            UndanginAja
                        </span>
                    </div>
                </a>
            </div>

            <div class="w-full bg-pink-100 sm:max-w-md border-2 border-white mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
