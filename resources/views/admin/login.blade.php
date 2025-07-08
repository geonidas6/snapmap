@extends('layouts.app')

@section('content')
    <h1 class="text-2xl mb-4">Admin Login</h1>
    <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-4">
        @csrf
        <div>
            <label for="email" class="block">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required class="border rounded w-full p-2">
            @error('email')
                <div class="text-red-500 text-sm">{{ $message }}</div>
            @enderror
        </div>
        <div>
            <label for="password" class="block">Password</label>
            <input id="password" type="password" name="password" required class="border rounded w-full p-2">
            @error('password')
                <div class="text-red-500 text-sm">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Login</button>
    </form>
@endsection
