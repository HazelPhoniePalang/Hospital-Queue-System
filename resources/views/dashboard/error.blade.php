@extends('layouts.app')

@section('content')
<div class="h-full min-h-[calc(100vh-100px)] flex flex-col items-center justify-center p-6 text-center">
    <div class="max-w-md w-full">
        <div class="inline-flex items-center justify-center w-24 h-24 bg-red-100 rounded-full text-red-500 mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-accent-black mb-2">Access Denied</h1>
        <p class="text-gray-500 mb-8">{{ $message }}</p>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-primary">Logout</button>
        </form>
    </div>
</div>
@endsection
