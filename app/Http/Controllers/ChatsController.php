<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Message;
use App\Sheep;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ChatsController extends Controller
{

//    public function __construct()
//    {
//        $this->middleware('auth');  // 登录用户才能访问
//    }

    /**
     * Fetch all messages
     *
     * @return Message
     */
    public function fetchMessages()
    {
        return Message::with('sheep')->get();
    }

    /**
     * Persist message to database
     *
     * @param  Request $request
     * @return Response
     */
    public function sendMessage(Request $request)
    {

        $sheep = auth()->user();

//        $sheep = Auth::user();

        $message = $sheep->messages()->create([
            'message' => $request->input('message')
        ]);

//        $message = $request['message'];

        event(
//            (new \App\Events\MessageSent($message)));
            (new \App\Events\MessageSent($sheep, $message)));
//            (new MessageSent($sheep, $message)));
//        broadcast(new MessageSent($sheep, $message))->toOthers();

        return response()->json(['status' => 'Message Sent!']);
    }

}
