<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCreatorProfileRequest;
use App\Services\StripeCreatorBillingService;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $creator = auth()->user();
        $profile = $creator->creatorProfile;

        return view('creator.profile.edit', compact('creator', 'profile'));
    }

    public function update(UpdateCreatorProfileRequest $request, StripeCreatorBillingService $billingService)
    {
        $creator = auth()->user();
        $profile = $creator->creatorProfile;

        $data = $request->validated();

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

        if ($creator->creator_approved_at) {
            $billingService->syncCreatorSubscriptionPrice($profile->fresh());
        }

        return redirect()
            ->route('creator.profile.edit')
            ->with('success', 'Profile updated successfully.');
    }
}
