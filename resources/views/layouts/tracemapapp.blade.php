<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="TraceMap">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="application-name" content="TraceMap">
    <meta name="msapplication-TileColor" content="#2563eb">
    <meta name="msapplication-navbutton-color" content="#2563eb">
    <meta name="msapplication-starturl" content="/?source=pwa">
    <meta name="format-detection" content="telephone=no">
    <title>TraceMap - Partagez vos moments géolocalisés</title>

    <!-- iOS PWA specific -->
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-touch-startup-image" content="/logo.png">

    <!-- iOS splash screens -->
    <link rel="apple-touch-startup-image" href="/icons/apple-splash-2048-2732.png" media="(device-width: 1024px) and (device-height: 1366px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">
    <link rel="apple-touch-startup-image" href="/icons/apple-splash-1668-2388.png" media="(device-width: 834px) and (device-height: 1194px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">
    <link rel="apple-touch-startup-image" href="/icons/apple-splash-1536-2048.png" media="(device-width: 768px) and (device-height: 1024px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">
    <link rel="apple-touch-startup-image" href="/icons/apple-splash-1125-2436.png" media="(device-width: 375px) and (device-height: 812px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)">
    <link rel="apple-touch-startup-image" href="/icons/apple-splash-1242-2688.png" media="(device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)">
    <link rel="apple-touch-startup-image" href="/icons/apple-splash-828-1792.png" media="(device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">
    <link rel="apple-touch-startup-image" href="/icons/apple-splash-1242-2208.png" media="(device-width: 414px) and (device-height: 736px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)">
    <link rel="apple-touch-startup-image" href="/icons/apple-splash-750-1334.png" media="(device-width: 375px) and (device-height: 667px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">
    <link rel="apple-touch-startup-image" href="/icons/apple-splash-640-1136.png" media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">

    <!-- Icons -->
    <link rel="apple-touch-icon" href="/icons/apple-icon-180x180.svg" sizes="180x180">
    <link rel="apple-touch-icon" href="/icons/apple-icon-152x152.svg" sizes="152x152">
    <link rel="apple-touch-icon" href="/icons/apple-icon-144x144.svg" sizes="144x144">
    <link rel="apple-touch-icon" href="/icons/apple-icon-120x120.svg" sizes="120x120">
    <link rel="icon" type="image/svg+xml" href="/icons/android-icon-192x192.svg" sizes="192x192">
    <link rel="icon" type="image/svg+xml" href="/icons/android-icon-96x96.svg" sizes="96x96">

    <!-- PWA  -->
    <meta name="theme-color" content="{{config('pwa.theme_color')}}"/>
    <link rel="apple-touch-icon" href="/logo.png">
    <link rel="manifest" href="/manifest.json" crossorigin="use-credentials">
    <!-- PWA end -->

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="anonymous" />
    <!-- Leaflet.Locate CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet.locatecontrol/dist/L.Control.Locate.min.css" />
    <script src="{{ asset('js/pwa-utils.js') }}"></script>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin="anonymous"></script>
    <!-- Leaflet.Locate JS -->
    <script src="https://cdn.jsdelivr.net/npm/leaflet.locatecontrol/dist/L.Control.Locate.min.js" charset="utf-8"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Intro.js CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intro.js@7.2.0/minified/introjs.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intro.js@7.2.0/themes/introjs-modern.min.css">

    <style>
        /* Styles personnalisés */
        .map-container {
            height: 70vh;
        }

        /* Styles pour le mode standalone (PWA) */
        html.standalone-mode {
            height: 100%;
            width: 100%;
            overflow: hidden;
        }

        html.standalone-mode body {
            height: 100%;
            width: 100%;
            overflow: hidden;
            margin: 0;
            padding: 0;
            position: fixed;
            /* Support pour les appareils avec encoche */
            padding: env(safe-area-inset-top) env(safe-area-inset-right) env(safe-area-inset-bottom) env(safe-area-inset-left);
        }

        html.standalone-mode #map {
            height: 100% !important;
            width: 100% !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            z-index: 10 !important;
        }

        /* Masquer la barre d'adresse sur iOS */
        @media screen and (orientation: portrait) {
            html.standalone-mode {
                min-height: calc(100% + 1px);
            }
        }

        /* Ajustements pour les appareils avec encoche */
        @supports (padding-top: env(safe-area-inset-top)) {
            html.standalone-mode body {
                padding-top: env(safe-area-inset-top);
                padding-bottom: env(safe-area-inset-bottom);
            }
        }
    </style>



    <!-- Matomo -->
    <script>
        var _paq = window._paq = window._paq || [];
        /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
        _paq.push(['trackPageView']);
        _paq.push(['enableLinkTracking']);
        (function() {
            var u="//stats.sefapanel.com/";
            _paq.push(['setTrackerUrl', u+'matomo.php']);
            _paq.push(['setSiteId', '1']);
            var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
            g.async=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
        })();
    </script>
    <!-- End Matomo Code -->

</head>
<body class="bg-gray-100 min-h-screen">
{{-- <header class="bg-blue-600 text-white shadow-md">
     <div class="container mx-auto px-4 py-4">
         <div class="flex justify-between items-center">
             <h1 class="text-2xl font-bold">TraceMap</h1>
             <nav>
                 <ul class="flex space-x-4">
                     <li><a href="{{ route('tracemap.index') }}" class="hover:underline">Carte</a></li>
                 </ul>
             </nav>
         </div>
     </div>
 </header>
--}}
<main class="container mx-auto px-4 py-6">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @yield('content')
</main>

{{-- <footer class="bg-gray-800 text-white py-4 mt-8">
     <div class="container mx-auto px-4 text-center">
         <p>&copy; {{ date('Y') }} TraceMap - Tous droits réservés</p>
     </div>
 </footer>--}}
<!-- Intro.js Script -->
<script src="https://cdn.jsdelivr.net/npm/intro.js@7.2.0/minified/intro.min.js"></script>

<!-- Service Worker Registration -->
<script>
    // Détecter si l'application est lancée depuis l'écran d'accueil
    const isInStandaloneMode = () => {
        return (window.matchMedia('(display-mode: standalone)').matches) ||
            (window.navigator.standalone) ||
            document.referrer.includes('android-app://');
    };

    // Appliquer des styles spécifiques si l'application est en mode standalone
    if (isInStandaloneMode()) {
        document.documentElement.classList.add('standalone-mode');

        // Masquer les éléments d'interface du navigateur si possible
        const metaViewport = document.querySelector('meta[name="viewport"]');
        if (metaViewport) {
            metaViewport.setAttribute('content', 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover');
        }
    }

    // Enregistrement du service worker
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/sw.js')
                .then(function(registration) {
                    console.log('Service Worker enregistré avec succès:', registration.scope);
                })
                .catch(function(error) {
                    console.error('Échec de l\'enregistrement du Service Worker:', error);
                });

            // Vérifier l'état du service worker
            navigator.serviceWorker.ready.then(function(registration) {
                console.log('Service Worker prêt avec scope:', registration.scope);
            });

            // Écouter les messages d'erreur du service worker
            navigator.serviceWorker.addEventListener('message', function(event) {
                if (event.data && event.data.error) {
                    console.error('Erreur du Service Worker:', event.data.error);
                }
            });
        });
    }

    // Détecter les changements de mode d'affichage
    window.matchMedia('(display-mode: standalone)').addEventListener('change', (evt) => {
        if (evt.matches) {
            console.log('L\'application est maintenant en mode standalone');
            document.documentElement.classList.add('standalone-mode');
        } else {
            console.log('L\'application n\'est plus en mode standalone');
            document.documentElement.classList.remove('standalone-mode');
        }
    });
</script>

@yield('scripts')
</body>
</html>
