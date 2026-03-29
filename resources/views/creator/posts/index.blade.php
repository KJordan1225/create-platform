@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">My Posts</h1>
            <p class="text-secondary mb-0">Manage your published and locked content.</p>
        </div>

        <div>
            @if (auth()->user()->canCreateCreatorPosts())
                <a href="{{ route('creator.posts.create') }}" class="btn btn-primary rounded-pill px-4">
                    New Post
                </a>
            @else
                <a href="{{ route('creator.billing.subscribe') }}" class="btn btn-warning rounded-pill px-4">
                    Unlock Posting
                </a>
            @endif
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success rounded-4 shadow-sm border-0">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger rounded-4 shadow-sm border-0">
            {{ session('error') }}
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            @forelse ($posts as $post)
                <div class="py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
                        <div>
                            <div class="fw-semibold mb-1">
                                {{ $post->is_locked ? 'Locked Post' : 'Public Post' }}
                            </div>
                            <div class="text-secondary small mb-2">
                                {{ $post->created_at->format('M d, Y g:i A') }}
                            </div>
                            <div>{{ \Illuminate\Support\Str::limit($post->caption, 180) }}</div>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('creator.posts.edit', $post) }}" class="btn btn-outline-primary btn-sm rounded-pill">
                                Edit
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-4">
                    <h2 class="h5 mb-2">No posts yet</h2>
                    <p class="text-secondary mb-3">Start publishing content for your subscribers.</p>

                    @if (auth()->user()->canCreateCreatorPosts())
                        <a href="{{ route('creator.posts.create') }}" class="btn btn-primary rounded-pill px-4">
                            Create First Post
                        </a>
                    @else
                        <a href="{{ route('creator.billing.subscribe') }}" class="btn btn-warning rounded-pill px-4">
                            Unlock Posting
                        </a>
                    @endif
                </div>
            @endforelse
        </div>
    </div>

    <div class="mt-4">
        {{ $posts->links() }}
    </div>
</div>
@endsection
