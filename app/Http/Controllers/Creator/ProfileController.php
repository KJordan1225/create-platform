<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function edit()
    {
        $creator = auth()->user();
        $profile = $creator->creatorProfile;

        return view('creator.profile.edit', compact('creator', 'profile'));
    }

    public function update(Request $request)
    {
        $creator = auth()->user();
        $profile = $creator->creatorProfile;

        $data = $request->validate([
            'display_name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:creator_profiles,slug,' . $profile->id],
            'bio' => ['nullable', 'string', 'max:5000'],
            'monthly_price' => ['required', 'numeric', 'min:1', 'max:999.99'],
            'allow_tips' => ['nullable', 'boolean'],
            'avatar' => ['nullable', 'image', 'max:4096'],
            'banner' => ['nullable', 'image', 'max:6144'],
        ]);

        if ($request->hasFile('avatar')) {
            if ($profile->avatar_path) {
                Storage::disk('public')->delete($profile->avatar_path);
            }

            $data['avatar_path'] = $request->file('avatar')->store('avatars', 'public');
        }

        if ($request->hasFile('banner')) {
            if ($profile->banner_path) {
                Storage::disk('public')->delete($profile->banner_path);
            }

            $data['banner_path'] = $request->file('banner')->store('banners', 'public');
        }

        $data['allow_tips'] = $request->boolean('allow_tips');

        $profile->update($data);

        return redirect()
            ->route('creator.profile.edit')
            ->with('success', 'Profile updated successfully.');
    }
}
