<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OneSignal;

class OnesignalController extends Controller
{
    public function OneSignalMsg(Request $request)
    {
        $onemsg=$request['onemsg'];
        $send = OneSignal::sendNotificationToAll(
                $onemsg,
//                $url=null,
                $data=[
                    'title'=> '加油，好嗎',
                    'message'=>'喵的，給力啊',
                ]
            );



        return response()->json(['msg' => '傳送成功']);
    }

}
