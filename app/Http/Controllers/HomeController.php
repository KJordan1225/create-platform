<?php

namespace App\Http\Controllers;

use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        $featuredCreators = User::query()
            ->where('role', 'creator')
            ->where('is_creator', true)
            ->where('is_active', true)
            ->whereNotNull('creator_approved_at')
            ->whereHas('creatorProfile', fn ($q) => $q->where('is_published', true))
            ->with('creatorProfile')
            ->latest()
            ->take(8)
            ->get();

        return view('home', compact('featuredCreators'));
    }
}
