@extends('layouts.app')

@section('content')
    <h1 class="text-xl font-bold mb-4">Admin Dashboard</h1>
    <form action="{{ route('admin.reset') }}" method="POST" class="mb-4">
        @csrf
        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded">Reset Database</button>
    </form>
@endsection
