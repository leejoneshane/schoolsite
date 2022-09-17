<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\PrivateMessage;

class MessagerController extends Controller
{

    public function send(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $message = $request->input('message');
        broadcast(new PrivateMessage($from, $to, $message));
    }

}
