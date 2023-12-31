<html>

<head>
    <title>Apol - @yield('title')</title>
    <meta name="viewport" content="width=device-width, viewport-fit=cover,initial-scale=1.0,user-scalable=no" />
    <script src="//unpkg.com/htmx.org@1.9.2/dist/htmx.min.js"></script>
    <link rel="manifest" href="{{ Helpers::get_base_url() }}/manifest.json">
    <script src="//cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>
    <script src="//cdn.jsdelivr.net/hls.js/latest/hls.js"></script>
    {{-- <script src="//cdn.dashjs.org/latest/dash.all.debug.js"></script> --}}
    <link rel="stylesheet" href="//cdn.plyr.io/3.7.8/plyr.css" />
    <meta name="mobile-web-app-capable" content="yes">
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="application-name" content="Apol">
    <meta name="apple-mobile-web-app-title" content="Apol">
    <meta name="theme-color" content="#121823">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <style>
        {!! Helpers::embed('apol.css') !!} {!! Helpers::embed('tinting.css') !!}
    </style>
    @if (Helpers::is_production())
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-GLLFFQ7PRP"></script>
        <script>
            window.gaId = 'G-GLLFFQ7PRP';
            window.dataLayer = window.dataLayer || [];
            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());
            gtag('config', window.gaId);
            document.addEventListener('DOMContentLoaded', function() {
                document.body.addEventListener('htmx:afterSwap', function(event) {
                    var newUrl = event.detail.xhr.responseURL;
                    gtag('config', window.gaId, {
                        'page_path': newUrl
                    });
                });
            });
        </script>
    @endif
</head>

<body class="tint-bg-down-5" id="body">
    @include('partials.title-bar', ['page_title' => $page_title])
    @if ((isset($is_content_fetch) && $is_content_fetch) || !$async_load)
        <div class="container tint-bg-down-0" id="content-container">
            @yield('content')
        </div>
    @else
        <div class="container tint-bg-down-0" id="content-container" hx-get="?fetch" hx-select="#content-container .listing" hx-swap="innerHTML" hx-trigger="load">
            @include('partials.ghost-cards')
        </div>
    @endif
    @include('partials.tab-bar')
    @include('partials.scroll-to-top')
    <script type="text/javascript">
        {!! Helpers::embed('apol.js') !!}
    </script>
</body>
</html>
