<!-- Stored in resources/views/layouts/app.blade.php -->
 
<html>
    <head>
        <title>Apol - @yield('title')</title>
        <meta name="viewport" content="width=device-width, viewport-fit=cover,initial-scale=1.0,user-scalable=no" />
        <script src="https://unpkg.com/htmx.org@1.9.2/dist/htmx.min.js"></script>
        <style>
            {!! Helpers::embed('apol.css') !!}
        </style>
    </head>
    <body>
        @if(isset($is_content_fetch) && $is_content_fetch || !$async_load)
            <div class="container">
                @yield('content')
            </div>
        @else
            <div class="container" hx-get="?fetch" hx-select=".container" hx-swap="outerHTML" hx-trigger="load">
                @include('partials.progress-indicator', ['text' => 'Loading…'])
            </div>
        @endif
        @include('partials.tab-bar')
    </body>
</html>