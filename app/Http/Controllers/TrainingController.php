<?php

namespace App\Http\Controllers;

use App\Models\TrainingRequest;
use App\Models\TrainingSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TrainingController
{
    public function index(Request $request): View
    {
        $sessions = TrainingSession::upcoming()->get();
        $selectedSession = $sessions->firstWhere('id', $request->integer('session'));

        return view('store.training', compact('sessions', 'selectedSession'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'request_type' => ['required', Rule::in(['session', 'entreprise', 'information'])],
            'training_session_id' => ['nullable', 'required_if:request_type,session', 'prohibited_unless:request_type,session', 'integer', Rule::exists('training_sessions', 'id')->where(fn ($q) => $q->where('is_published', true)->where('starts_at', '>', now()))],
            'name' => ['required', 'string', 'min:2', 'max:150'],
            'company' => ['nullable', 'required_if:request_type,entreprise', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'min:7', 'max:40'],
            'participants' => ['required', 'integer', 'min:1', 'max:1000'],
            'preferred_period' => ['nullable', 'string', 'max:150'],
            'message' => ['nullable', 'string', 'max:3000'],
            'consent' => ['accepted'],
            'website' => ['nullable', 'size:0'],
        ]);
        unset($data['consent'], $data['website']);
        $data['session_title'] = isset($data['training_session_id']) ? TrainingSession::find($data['training_session_id'])?->title : null;
        $data['consented_at'] = now();
        $data['status'] = 'new';
        TrainingRequest::create($data);

        return redirect()->to(route('training.index').'#demande')->with('success', 'Votre demande de formation a été enregistrée. Notre équipe vous recontactera pour confirmer les modalités.');
    }
}
