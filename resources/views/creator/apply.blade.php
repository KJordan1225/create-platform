@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xl-9">
        <div class="mb-4">
            <h1 class="h2 mb-1">Apply as a Creator</h1>
            <p class="text-secondary mb-0">Set up your profile and submit it for approval.</p>
        </div>

        <div class="bg-panel rounded-4 p-3 p-md-4">
            <form method="POST" action="{{ route('creator.apply.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="row g-4">
                    <div class="col-12 col-md-6">
                        <label class="form-label">Display Name</label>
                        <input type="text" name="display_name" class="form-control" value="{{ old('display_name') }}">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" class="form-control" value="{{ old('slug') }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Bio</label>
                        <textarea name="bio" rows="6" class="form-control">{{ old('bio') }}</textarea>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label">Monthly Price</label>
                        <input type="number" name="monthly_price" step="0.01" min="1" max="999.99"
                               class="form-control" value="{{ old('monthly_price', '9.99') }}">
                    </div>

                    <div class="col-12 col-md-6 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="allow_tips" value="1"
                                   id="allow_tips" {{ old('allow_tips', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="allow_tips">Allow tips</label>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label">Avatar</label>
                        <input type="file" name="avatar" class="form-control">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label">Banner</label>
                        <input type="file" name="banner" class="form-control">
                    </div>

                    <div class="col-12">
                        <button class="btn btn-primary">Submit Application</button>
                    </div>
resources/views/components                </div>
            </form>
        </div>
    </div>
</div>
@endsection
