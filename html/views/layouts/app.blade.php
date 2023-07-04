<!-- Stored in resources/views/layouts/app.blade.php -->
 
<html>
    <head>
        <title>Apol - @yield('title')</title>
        <meta name="viewport" content="width=device-width, viewport-fit=cover,initial-scale=1.0,user-scalable=no" />
        <script src="https://unpkg.com/htmx.org@1.9.2/dist/htmx.min.js"></script>
        <style>
            {!! Helpers::embed('apol.css') !!}
        </style>
        @if(Helpers::is_production())
            <script async src="https://www.googletagmanager.com/gtag/js?id=G-GLLFFQ7PRP"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                gtag('config', 'G-GLLFFQ7PRP');
                // detect url change

                gtag('event', 'page_view', {
                    'page_title' : document.title,
                    'page_location' : window.location.href,
                    'page_path' : window.location.pathname
                });

                window.addEventListener('load', function() {
                    var currentUrl = window.location.href;
                    var observer = new MutationObserver(function(mutations) {
                        mutations.forEach(function(mutation) {
                            if (currentUrl != window.location.href) {
                                currentUrl = window.location.href;
                                gtag('event', 'page_view', {
                                    'page_title' : document.title,
                                    'page_location' : window.location.href,
                                    'page_path' : window.location.pathname
                                });
                            }
                        });
                    });
                    observer.observe(document.querySelector('body'), {childList: true, subtree: true});
                });
            </script>
        @endif
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