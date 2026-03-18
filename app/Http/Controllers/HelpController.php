<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class HelpController extends Controller
{
    public function index(): View
    {
        return view('help.index');
    }

    public function creatorGuide(): View
    {
        return view('help.creator-guide');
    }

    public function fanGuide(): View
    {
        return view('help.fan-guide');
    }

    public function adminGuide(): View
    {
        return view('help.admin-operations');
    }
}
