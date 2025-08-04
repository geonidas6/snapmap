@extends('layouts.app')


@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-semibold text-gray-800">Gestion des Tracemaps</h1>
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded transition">Retour au Dashboard</a>
                    </div>

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <!-- Filtres et recherche -->
                    <div class="mb-6">
                        <form action="{{ route('admin.tracemaps') }}" method="GET" class="flex space-x-4">
                            <div class="flex-1">
                                <input type="text" name="search" value="{{ $search }}" placeholder="Rechercher par ID, latitude ou longitude..."
                                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition">
                                Rechercher
                            </button>
                            @if($search)
                                <a href="{{ route('admin.tracemaps') }}" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg transition">
                                    Réinitialiser
                                </a>
                            @endif
                        </form>
                    </div>

                    <!-- Liste des tracemaps -->
                    <form action="{{ route('admin.tracemaps.delete') }}" method="POST">
                        @csrf
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        </th>
                                        <th class="px-4 py-2 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th class="px-4 py-2 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Latitude</th>
                                        <th class="px-4 py-2 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Longitude</th>
                                        <th class="px-4 py-2 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Médias</th>
                                        <th class="px-4 py-2 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date de création</th>
                                        <th class="px-4 py-2 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @forelse($tracemaps as $tracemap)
                                        <tr>
                                            <td class="px-4 py-2 whitespace-nowrap">
                                                <input type="checkbox" name="tracemap_ids[]" value="{{ $tracemap->id }}" class="tracemap-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                            </td>
                                            <td class="px-4 py-2 whitespace-nowrap">{{ $tracemap->id }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap">{{ $tracemap->latitude }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap">{{ $tracemap->longitude }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap">{{ $tracemap->media->count() }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap">{{ $tracemap->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap">
                                                <a href="{{ route('admin.tracemaps.edit', $tracemap) }}" class="text-blue-600 hover:text-blue-900">Modifier</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-4 py-2 text-center text-gray-500">Aucun tracemap trouvé</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 flex justify-between items-center">
                            <div>
                                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded transition" onclick="return confirm('Êtes-vous sûr de vouloir supprimer les tracemaps sélectionnés ?')">
                                    Supprimer les sélectionnés
                                </button>
                            </div>
                            <div>
                                {{ $tracemaps->links() }}
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Script pour sélectionner/désélectionner tous les tracemaps
        document.getElementById('select-all').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.tracemap-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    </script>
@endsection
