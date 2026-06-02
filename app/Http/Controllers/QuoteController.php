<?php

namespace App\Http\Controllers;

use App\Models\QuoteRequest;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'company' => 'nullable|string|max:255',
            'country' => 'required|string|max:255',
            'product' => 'nullable|string|max:255',
            'term' => 'nullable|string|max:50',
            'seats' => 'nullable|string|max:50',
            'message' => 'nullable|string|max:5000',
        ]);

        QuoteRequest::create($request->only([
            'name', 'email', 'company', 'country',
            'product', 'term', 'seats', 'message',
        ]));

        // TODO: Send notification email to admin

        return response()->json(['success' => true, 'message' => 'Quote request submitted successfully.']);
    }
}
