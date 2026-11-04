@extends('layouts.store')
@section('title', 'Connexion à l’administration | Vigilus Guinée')
@section('robots', 'noindex,nofollow')
@section('content')
<div class="container-wide max-w-lg py-20">
    <div class="border border-slate-200 rounded-2xl p-8 bg-white">
        <p class="kicker">Espace réservé</p>
        <h1 class="text-3xl font-extrabold mt-2">Administration</h1>
        @if($errors->any())<div class="bg-red-50 text-red-900 p-4 rounded-lg mt-5" role="alert">{{ $errors->first() }}</div>@endif
        <form action="{{ route('login.store') }}" method="POST" class="mt-7 space-y-5">
            @csrf
            <div><label for="email" class="input-label">Email administrateur</label><input id="email" class="field" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"></div>
            <div><label for="password" class="input-label">Mot de passe</label><input id="password" class="field" type="password" name="password" required autocomplete="current-password"></div>
            <button type="submit" class="btn-red w-full">Se connecter</button>
        </form>
    </div>
</div>
@endsection
