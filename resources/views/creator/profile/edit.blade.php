@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xl-9">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-1">Edit Creator Profile</h1>
                <p class="text-secondary mb-0">Update how your page appears to fans.</p>
            </div>
        </div>

        <div class="bg-panel rounded-4 p-3 p-md-4">
            <form method="POST" action="{{ route('creator.profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <div class="col-12 col-md-6">
                        <label class="form-label">Display Name</label>
                        <input type="text" name="display_name" class="form-control"
                               value="{{ old('display_name', $profile->display_name) }}">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" class="form-control"
                               value="{{ old('slug', $profile->slug) }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Bio</label>
                        <textarea name="bio" rows="6" class="form-control">{{ old('bio', $profile->bio) }}</textarea>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label">Monthly Price</label>
                        <input type="number" name="monthly_price" step="0.01" min="1" max="999.99"
                               class="form-control"
                               value="{{ old('monthly_price', $profile->monthly_price) }}">
                    </div>

                    <div class="col-12 col-md-6 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="allow_tips" value="1"
                                   id="allow_tips"
                                   {{ old('allow_tips', $profile->allow_tips) ? 'checked' : '' }}>
                            <label class="form-check-label" for="allow_tips">Allow tips</label>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label">Avatar</label>
                        <input type="file" name="avatar" class="form-control">
                        @if($profile->avatar_path)
                            <img src="{{ $profile->avatar_url }}" alt="Avatar" class="rounded-circle mt-3" width="96" height="96" style="object-fit: cover;">
                        @endif
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label">Banner</label>
                        <input type="file" name="banner" class="form-control">
                        @if($profile->banner_path)
                            <img src="{{ $profile->banner_url }}" alt="Banner" class="img-fluid rounded mt-3 w-100" style="max-height: 180px; object-fit: cover;">
                        @endif
                    </div>

                    <div class="col-12">
                        <button class="btn btn-primary">Save Profile</button>
                        <a href="{{ route('creator.dashboard') }}" class="btn btn-outline-light ms-2">Back</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
