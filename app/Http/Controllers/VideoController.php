<?php

namespace App\Http\Controllers;

use App\Models\PostMedia;

class VideoController extends Controller
{
    public function show(PostMedia $media)
    {
        abort_unless(str_starts_with($media->mime_type, 'video'), 404);

        return view('videos.show', compact('media'));
    }
	
	public function stream(PostMedia $media)
	{
		      
        $filename = str_replace('posts/', '', $media->file_path);

        $path = public_path('images/posts/' . $filename);

		abort_unless(file_exists($path), 404);

		$size = filesize($path);
		$start = 0;
		$end = $size - 1;

		$headers = [
			'Content-Type' => 'video/mp4',
			'Accept-Ranges' => 'bytes',
		];

		if (request()->hasHeader('Range')) {
			$range = request()->header('Range');

			if (preg_match('/bytes=(\d+)-(\d*)/', $range, $matches)) {
				$start = (int) $matches[1];
				$end = $matches[2] !== '' ? (int) $matches[2] : $end;
			}

			$length = $end - $start + 1;

			$headers['Content-Length'] = $length;
			$headers['Content-Range'] = "bytes $start-$end/$size";

			$stream = function () use ($path, $start, $length) {
				$handle = fopen($path, 'rb');
				fseek($handle, $start);

				$remaining = $length;
				while ($remaining > 0 && !feof($handle)) {
					$read = ($remaining > 8192) ? 8192 : $remaining;
					echo fread($handle, $read);
					$remaining -= $read;
					flush();
				}

				fclose($handle);
			};

			return response()->stream($stream, 206, $headers);
		}

		$headers['Content-Length'] = $size;

		return response()->stream(function () use ($path) {
			readfile($path);
		}, 200, $headers);
	}

}
