<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ExploreController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->get('search'));

        $creators = User::query()
            ->where('role', 'creator')
            ->where('is_creator', true)
            ->where('is_active', true)
            ->whereNotNull('creator_approved_at')
            ->whereHas('creatorProfile', function ($query) use ($search) {
                $query->where('is_published', true);

                if ($search !== '') {
                    $query->where(function ($q) use ($search) {
                        $q->where('display_name', 'like', "%{$search}%")
                          ->orWhere('bio', 'like', "%{$search}%")
                          ->orWhere('slug', 'like', "%{$search}%");
                    });
                }
            })
            ->with('creatorProfile')
            ->paginate(12)
            ->withQueryString();

        return view('explore.index', compact('creators', 'search'));
    }
}
