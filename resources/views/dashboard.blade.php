@extends('layouts.app')

@section('content')
    <h1 class="text-xl font-bold mb-6">Admin Dashboard</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Mode Maintenance -->
        <div class="border rounded-lg p-4 bg-gray-50">
            <h2 class="text-lg font-semibold mb-4">Mode Maintenance</h2>

            <div class="flex flex-col space-y-4">
                <div class="flex space-x-2">
                    <form action="{{ route('admin.maintenance.on') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Activer le mode maintenance</button>
                    </form>

                    <form action="{{ route('admin.maintenance.off') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Désactiver le mode maintenance</button>
                    </form>
                </div>

                <div class="mt-4">
                    <p class="mb-2 font-medium">URL de bypass du mode maintenance :</p>
                    <div class="flex items-center">
                        <input type="text" value="{{config('app.maintenance_app_url').'/maintenance-JM7r68d9Z08D8riXtjJp9ht6tqB3UZRzSIo'}}" class="w-full p-2 border rounded-l bg-gray-100 text-gray-800" readonly id="secretUrl">
                        <button onclick="copyToClipboard()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-r transition">Copier</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reset Database -->
        <div class="border rounded-lg p-4 bg-gray-50">
            <h2 class="text-lg font-semibold mb-4">Base de données</h2>
            <form action="{{ route('admin.reset') }}" method="POST" class="mb-4">
                @csrf
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded transition">Réinitialiser la base de données</button>
            </form>
            <p class="text-sm text-gray-600">Attention : cette action supprimera toutes les données et réinitialisera la base de données.</p>
        </div>
    </div>

    <script>
        function copyToClipboard() {
            const secretUrl = document.getElementById('secretUrl');
            secretUrl.select();
            document.execCommand('copy');
            alert('URL copiée dans le presse-papier !');
        }
    </script>
@endsection
