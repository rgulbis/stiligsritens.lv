<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen antialiased bg-[url('/public/bikebg.png')] bg-cover bg-center bg-no-repeat">
        <div class="relative grid h-dvh flex-col items-center justify-center md:px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
            <div class="bg-muted relative hidden h-full flex-col p-10 text-white lg:flex dark:border-e dark:border-neutral-800">
                <div class="absolute inset-0 w-screen z-0 bg-[url('/public/bikebg.png')] bg-cover bg-center bg-no-repeat"></div>                
                <a href="{{ route('home') }}" class="relative z-20 flex items-center text-lg font-medium" wire:navigate>
                    <span class="flex h-10 w-10 items-center justify-center rounded-md">
                        <x-app-logo-icon class="me-2 h-7 fill-current text-white" />
                    </span>
                    {{ config('app.name', 'Laravel') }}
                </a>

                @php
                    [$message = 'Tavs sapņu ritens sākas tieši šeit!'];
                @endphp

                <div class="relative z-20 mt-auto">
                    <blockquote class="space-y-2">
                        <flux:heading size="xl" class="text-white">{{ trim($message) }}</flux:heading>
                    </blockquote>
                </div>
            </div>
            <div class="bg-white/35 dark:bg-black/50 backdrop-blur-sm grid w-fit rounded-xl  lg:bg-white/20 dark:lg:bg-black/25 lg:p-8 justify-self-center">
                <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[350px] p-6">
                    <a href="{{ route('home') }}" class="z-20 flex flex-col items-center gap-2 font-medium lg:hidden" wire:navigate>
                        <span class="flex h-9 w-9 items-center justify-center rounded-md">
                            <x-app-logo-icon class="size-9 fill-current text-black dark:text-white" />
                        </span>

                        <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                    </a>
                    {{ $slot }}
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
