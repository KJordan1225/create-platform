@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xl-9">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-1">Create Post</h1>
                <p class="text-secondary mb-0">Publish content for your fans and subscribers.</p>
            </div>
        </div>

        <div class="bg-panel rounded-4 p-3 p-md-4">
            <form method="POST" action="{{ route('creator.posts.store') }}" enctype="multipart/form-data">
                @csrf

                @php($post = new \App\Models\Post())
                @include('creator.posts._form')

                <div class="mt-4">
                    <button class="btn btn-primary">Create Post</button>
                    <a href="{{ route('creator.posts.index') }}" class="btn btn-outline-light ms-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection