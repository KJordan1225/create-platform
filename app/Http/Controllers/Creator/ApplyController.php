<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApplyForCreatorRequest;
use App\Models\CreatorProfile;
use Illuminate\Support\Facades\Storage;

class ApplyController extends Controller
{
    public function create()
    {
        $user = auth()->user();

        if ($user->isCreator()) {
            return redirect()->route('creator.dashboard');
        }

        return view('creator.apply');
    }

    public function store(ApplyForCreatorRequest $request)
    {
        $user = $request->user();

        if ($user->is_creator) {
            return redirect()->route('dashboard')->with('success', 'Your creator application already exists.');
        }

        $data = $request->validated();

        if ($request->hasFile('avatar')) {
            $data['avatar_path'] = $request->file('avatar')->store('avatars', 'public');
        }

        if ($request->hasFile('banner')) {
            $data['banner_path'] = $request->file('banner')->store('banners', 'public');
        }

        $data['allow_tips'] = $request->boolean('allow_tips');
        $data['user_id'] = $user->id;
        $data['is_published'] = false;

        CreatorProfile::create($data);

        $user->update([
            'role' => 'creator',
            'is_creator' => true,
            'creator_approved_at' => null,
        ]);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Your creator application was submitted and is awaiting approval.');
    }
}
