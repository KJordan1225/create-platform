<div class="row g-4">
    <div class="col-12">
        <label class="form-label">Caption</label>
        <textarea name="caption" rows="6" class="form-control">{{ old('caption', $post->caption ?? '') }}</textarea>
    </div>

    <div class="col-12">
        <label class="form-label">Upload Media</label>
        <input type="file" name="media[]" class="form-control" multiple>
        <div class="form-text">You can upload images or short videos.</div>
    </div>

    <div class="col-12 col-md-6">
        <div class="form-check form-switch">
            <input type="checkbox" class="form-check-input" name="is_locked" value="1" id="is_locked"
                   {{ old('is_locked', $post->is_locked ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_locked">Locked for subscribers only</label>
        </div>
    </div>

    <div class="col-12 col-md-6">
        <div class="form-check form-switch">
            <input type="checkbox" class="form-check-input" name="is_published" value="1" id="is_published"
                   {{ old('is_published', $post->is_published ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_published">Publish now</label>
        </div>
    </div>

    @if($post->exists && $post->media->count())
        <div class="col-12">
            <label class="form-label">Existing Media</label>
            <div class="row g-3">
                @foreach($post->media as $media)
                    <div class="col-6 col-md-4 col-xl-3">
                        <div class="border rounded-4 overflow-hidden">
                            @if($media->media_type === 'video')
                                <video class="w-100" controls style="height: 180px; object-fit: cover;">
                                    <source src="{{ $media->url }}" type="{{ $media->mime_type }}">
                                </video>
                            @else
                                <img src="{{ $media->url }}" class="w-100" style="height: 180px; object-fit: cover;" alt="">
                            @endif

                            <div class="p-2">
                                <button type="submit"
                                        form="delete-media-{{ $media->id }}"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Remove this media item?')">
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>