<?php

namespace App\Http\Controllers\Admin;

use App\Models\TrainingRequest;
use App\Models\TrainingSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TrainingController
{
    public function index(): View
    {
        return view('admin.training.index', [
            'sessions' => TrainingSession::orderByDesc('starts_at')->paginate(10, ['*'], 'sessions_page'),
            'requests' => TrainingRequest::latest()->paginate(20, ['*'], 'requests_page'),
        ]);
    }

    public function create(): View
    {
        return view('admin.training.form', ['trainingSession' => new TrainingSession()]);
    }

    public function edit(TrainingSession $trainingSession): View
    {
        return view('admin.training.form', compact('trainingSession'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'description' => ['required', 'string', 'max:5000'],
            'starts_at' => ['required', 'date'],
            'location' => ['required', 'string', 'max:200'],
            'audience' => ['required', 'string', 'max:160'],
            'is_published' => ['required', 'boolean'],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        TrainingSession::create($this->validated($request));
        return redirect()->route('admin.training.index')->with('success', 'Session créée.');
    }

    public function update(Request $request, TrainingSession $trainingSession): RedirectResponse
    {
        $trainingSession->update($this->validated($request));
        return redirect()->route('admin.training.index')->with('success', 'Session mise à jour.');
    }

    public function updateRequest(Request $request, TrainingRequest $trainingRequest): RedirectResponse
    {
        $trainingRequest->update($request->validate(['status' => ['required', Rule::in(['new', 'contacted', 'closed'])]]));
        return back()->with('success', 'Suivi de la demande mis à jour.');
    }
}
