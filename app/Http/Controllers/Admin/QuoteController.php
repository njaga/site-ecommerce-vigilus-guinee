<?php

namespace App\Http\Controllers\Admin;

use App\Models\QuoteRequest;
use Illuminate\View\View;

class QuoteController
{
    public function index(): View
    {
        return view('admin.quotes', [
            'quotes' => QuoteRequest::with('product')->latest()->paginate(30),
        ]);
    }
}
