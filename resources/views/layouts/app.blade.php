<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title . ' - ' . config('app.name', 'UndanginAja') : config('app.name', 'UndanginAja') }}</title>
        
        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('build/assets/logo.png') }}">
        <link rel="shortcut icon" type="image/png" href="{{ asset('build/assets/logo.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('build/assets/logo.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <!-- <audio autoplay loop hidden>
        <source src="{{ asset('audio/hey.mp3') }}" type="audio/mpeg">
        Browser lo ga support audio.
    </audio> -->
    <body class="font-sans">
        <div class="min-h-screen bg-green">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-dark shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="bg-fuchsia-50">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
