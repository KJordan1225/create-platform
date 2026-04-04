@extends('layouts.app')

@section('content')
<div class="card shadow-sm rounded-4 border-0">
    <div class="card-body p-3">
        <video
            id="uploadedVideo"
            class="w-100 rounded-3"
            controls
            preload="metadata"
        >
            <source src="{{ route('videos.stream', $filename) }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>

        <div class="d-flex flex-wrap gap-2 mt-3">
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="skipVideo(-10)">
                « Rewind 10s
            </button>

            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="skipVideo(-5)">
                ‹ Rewind 5s
            </button>

            <button type="button" class="btn btn-primary btn-sm" onclick="togglePlayPause()">
                Play / Pause
            </button>

            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="skipVideo(5)">
                Forward 5s ›
            </button>

            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="skipVideo(10)">
                Forward 10s »
            </button>
        </div>

        <div class="d-flex flex-wrap gap-2 mt-3">
            <button type="button" class="btn btn-outline-dark btn-sm" onclick="setPlaybackRate(0.5)">
                0.5x
            </button>
            <button type="button" class="btn btn-outline-dark btn-sm" onclick="setPlaybackRate(1)">
                1x
            </button>
            <button type="button" class="btn btn-outline-dark btn-sm" onclick="setPlaybackRate(1.5)">
                1.5x
            </button>
            <button type="button" class="btn btn-outline-dark btn-sm" onclick="setPlaybackRate(2)">
                2x
            </button>
        </div>

        <div class="mt-3 small text-muted">
            Speed: <span id="playbackRateLabel">1x</span>
        </div>
    </div>
</div>

<script>
    function getVideo() {
        return document.getElementById('uploadedVideo');
    }

    function skipVideo(seconds) {
        const video = getVideo();

        if (!video.duration || isNaN(video.duration)) {
            return;
        }

        let newTime = video.currentTime + seconds;

        if (newTime < 0) {
            newTime = 0;
        }

        if (newTime > video.duration) {
            newTime = video.duration;
        }

        video.currentTime = newTime;
    }

    function togglePlayPause() {
        const video = getVideo();

        if (video.paused) {
            video.play();
        } else {
            video.pause();
        }
    }

    function setPlaybackRate(rate) {
        const video = getVideo();
        video.playbackRate = rate;
        document.getElementById('playbackRateLabel').textContent = rate + 'x';
    }
</script>
@endsection