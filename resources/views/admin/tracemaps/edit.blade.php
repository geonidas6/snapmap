@extends('layouts.app')


@section('content')
    <div class="container mx-auto px-4 sm:px-8">
        <div class="py-8">
            <div>
                <h2 class="text-2xl font-semibold leading-tight">Modifier le Tracemap #{{ $tracemap->id }}</h2>
            </div>
            <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
                <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
                    <form action="{{ route('admin.tracemaps.update', $tracemap) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="px-5 py-5 bg-white border-b border-gray-200">
                            <div class="mb-5">
                                <label for="latitude" class="block text-gray-700 text-sm font-bold mb-2">Latitude:</label>
                                <input type="text" name="latitude" id="latitude" value="{{ old('latitude', $tracemap->latitude) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('latitude')
                                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-5">
                                <label for="longitude" class="block text-gray-700 text-sm font-bold mb-2">Longitude:</label>
                                <input type="text" name="longitude" id="longitude" value="{{ old('longitude', $tracemap->longitude) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('longitude')
                                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-5">
                                <label for="message" class="block text-gray-700 text-sm font-bold mb-2">Message:</label>
                                <textarea name="message" id="message" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('message', $tracemap->message) }}</textarea>
                                @error('message')
                                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="px-5 py-5 bg-white border-t flex justify-end">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
