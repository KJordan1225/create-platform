<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Starz') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* body {
            background-color: #0f1115;
            color: #f5f7fa;
        } */

        body {
            background-color: #f5f7fa;
            color: #0f1115;
        }

        /* .bg-panel {
            background: #181c23;
            border: 1px solid rgba(255,255,255,.06);
        } */
        .bg-panel {
            background: #f5f7fa;
            border: 1px solid rgba(255,255,255,.06);
        }

        /* .creator-card, */
        /* .post-card {
            background: #181c23;
            border: 1px solid rgba(255,255,255,.06);
            border-radius: 1rem;
            overflow: hidden;
        } */

        .creator-card,    
        .post-card {
            background: #f5f7fa;
            border: 1px solid rgba(255,255,255,.06);
            border-radius: 1rem;
            overflow: hidden;
        }

        /* .locked-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,.55);
            backdrop-filter: blur(6px);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            padding: 1rem;
        } */

        .locked-overlay {
            position: absolute;
            inset: 0;
            background: rgba(255,255,255,1);
            backdrop-filter: blur(6px);
            display: flex;
            align-items: center;
            justify-content: center;
            color: black;
            text-align: center;
            padding: 1rem;
        }

        .banner-cover {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }

        .avatar-lg {
            width: 96px;
            height: 96px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #0f1115;
        }

        .media-thumb {
            width: 480px;
            height: 480px;
            object-fit: cover;
        }

        @media (max-width: 767.98px) {
            .banner-cover {
                height: 160px;
            }

            .media-thumb {
                height: 220px;
            }
        }
    </style>
</head>
<body>
    @include('partials.navbar')

    <main class="py-4">
        <div class="container">
            @include('partials.flash')
            @yield('content')
        </div>
    </main>

    @include('partials.footer')
</body>
</html>
