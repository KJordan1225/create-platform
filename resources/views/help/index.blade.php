@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xl-10">
        <div class="mb-4">
            <h1 class="h2 mb-1">Help Center</h1>
            <p class="text-secondary mb-0">
                Choose a guide below to learn how to use the platform.
            </p>
        </div>

        <div class="row g-4">
            <div class="col-12 col-lg-4">
                <div class="bg-panel rounded-4 p-4 h-100">
                    <h2 class="h4 mb-2">Creator User Guide</h2>
                    <p class="text-secondary">
                        Learn how to set up your creator profile, publish posts, receive subscriptions, accept tips, and manage earnings.
                    </p>
                    <a href="{{ route('help.creator') }}" class="btn btn-primary w-100">
                        Open Creator Guide
                    </a>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="bg-panel rounded-4 p-4 h-100">
                    <h2 class="h4 mb-2">Fan User Guide</h2>
                    <p class="text-secondary">
                        Learn how to explore creators, subscribe, tip, message, comment, and manage your feed and subscriptions.
                    </p>
                    <a href="{{ route('help.fan') }}" class="btn btn-primary w-100">
                        Open Fan Guide
                    </a>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="bg-panel rounded-4 p-4 h-100">
                    <h2 class="h4 mb-2">Admin Operations Guide</h2>
                    <p class="text-secondary">
                        Learn how to review creators, moderate content, manage reports, monitor analytics, and keep operations healthy.
                    </p>
                    <a href="{{ route('help.admin') }}" class="btn btn-primary w-100">
                        Open Admin Guide
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
