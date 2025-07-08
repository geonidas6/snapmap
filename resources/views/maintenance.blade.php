<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Site en maintenance</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="text-center">
                <h1 class="text-2xl font-bold text-gray-900 mb-4">Site en maintenance</h1>
                <p class="text-gray-600 mb-6">Nous effectuons actuellement des travaux de maintenance sur notre site. Nous serons de retour très bientôt.</p>
                
                @if(isset($secret))
                <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                    <p class="text-sm text-yellow-700">Vous accédez au site en mode maintenance grâce à un lien secret.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>