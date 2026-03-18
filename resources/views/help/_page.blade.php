@props([
    'title' => 'Help',
    'subtitle' => null,
])

@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xl-10">
        <div class="mb-4">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 mb-1">{{ $title }}</h1>
                    @if($subtitle)
                        <p class="text-secondary mb-0">{{ $subtitle }}</p>
                    @endif
                </div>

                <a href="{{ route('help.index') }}" class="btn btn-outline-light btn-sm">
                    Back to Help Center
                </a>
            </div>
        </div>

        <div class="bg-panel rounded-4 p-3 p-md-4">
            {{ $slot }}
        </div>
    </div>
</div>
@endsection
