<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - Team Buzzer</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Roboto+Mono:wght@400;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
        
        <style>
            /* Prevent pull-to-refresh on mobile */
            body {
                overscroll-behavior-y: contain;
            }
            
            /* Prevent text selection on buzzer button */
            button {
                -webkit-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                user-select: none;
                -webkit-tap-highlight-color: transparent;
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen">
            {{ $slot }}
        </div>

        @stack('modals')
        @livewireScripts
        @stack('scripts')
    </body>
</html>