<!-- Stored in resources/views/layouts/app.blade.php -->
 
<html>
    <head>
        <title>Apol - @yield('title')</title>
        <meta name="viewport" content="width=device-width, viewport-fit=cover,initial-scale=1.0,user-scalable=no" />
        <script src="https://unpkg.com/htmx.org@1.9.2"></script>
        <style>
            {!! Helpers::embed('apol.css') !!}
        </style>
    </head>
    <body>
        <div class="container">
            @yield('content')
        </div>
        @include('partials.tab-bar')
    </body>
</html>