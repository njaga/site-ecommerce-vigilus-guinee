<?php

namespace App\Http\Controllers\Admin;

use App\Services\LaunchPreflight;
use Illuminate\View\View;

class LaunchReadinessController
{
    public function __invoke(LaunchPreflight $preflight): View
    {
        $checks = $preflight->checks();
        return view('admin.launch-readiness', compact('checks'));
    }
}
