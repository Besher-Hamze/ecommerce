<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
class AdminController extends Controller
{
   public function index()
   {
    return view('dashboard.master');
   }
   public function showMessgae()
   {
      $messages = Message::all();

      return view('dashboard.messages', compact('messages'));
   }
   
}
