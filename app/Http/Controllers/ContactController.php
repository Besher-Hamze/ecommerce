<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;
class ContactController extends Controller
{
    //
	public function index()
	{
		return view('website.contact');
	}
	public function store(Request $request)
    {
        // Validation
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'message' => 'required|string|min:5',
        ]);

        // Store message
        Message::create([
            'user_id' => Auth::check() ? Auth::id() : null,
            'name'    => $request->name,
            'email'   => $request->email,
            'message' => $request->message,
        ]);
		return back()->with('success', 'Your message has been sent successfully.');
	}
}
