@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xl-9">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-1">Edit Post</h1>
                <p class="text-secondary mb-0">Update your content and media.</p>
            </div>
        </div>

        <div class="bg-panel rounded-4 p-3 p-md-4">
            <form method="POST" action="{{ route('creator.posts.update', $post) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @include('creator.posts._form')

                <div class="mt-4">
                    <button class="btn btn-primary" type="submit">Save Changes</button>
                    <a href="{{ route('creator.posts.index') }}" class="btn btn-primary ms-2">Back</a>
                </div>
            </form>

            @if($post->exists && $post->media->count())
                @foreach($post->media as $media)
                    <form id="delete-media-{{ $media->id }}"
                          method="POST"
                          action="{{ route('creator.posts.media.destroy', [$post, $media]) }}"
                          class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection