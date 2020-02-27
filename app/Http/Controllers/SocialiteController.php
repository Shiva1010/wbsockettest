<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Socialite;

require_once __DIR__ . '/vendor/autoload.php';

class SocialiteController extends Controller
{

    protected $providers = [
        'google',
    ];

    /**
     * Redirect the user to the Google authentication page.
     *
     * @return 301
     */

    public function RedirectToProvider()
    {

        try {
            return Socialite::driver('google')->redirect();
        } catch (Exception $e) {
            // You should show something simple fail message
            return $this->sendFailedResponse($e->getMessage());
        }

    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */

    public function HandleProviderCallback()
    {
        $user =Socialite::driver('google')->user();

        $email=$user->email;
        $token=$user->token;
        return response()->json(['email'=>$email,'token' => $token]);
//        dd($user)S;
    }

    public  function CheckToken(Request $request)
    {
        //        $sheep_email = $request['email'];
        $token = $request['token'];

        $user =Socialite::driver('google')->userFromToken($token);

        dd($user);
    }

    public function CheckAndroidToken(Request $request)
    {
        $token = $request['token'];

        $androidUser = Socialite::driver('googleandroid')->userFromToken($token);

        dd($androidUser);


    }


    public function TestCheckAndroidToken(Request $request)
    {
//        require_once 'vendor/autoload.php';

        // Get $id_token via HTTPS POST.

        $CLIENT_ID ='431122609682-9kt3dot3fjeq92rkar83mor16siod2ch.apps.googleusercontent.com';
        $id_token = $request['id_token'];
        $client = new Google_Client(['client_id' => $CLIENT_ID]);  // Specify the CLIENT_ID of the app that accesses the backend
        $payload = $client->verifyIdToken($id_token);
//        if ($payload) {
//            $userid = $payload['sub'];
//            // If request specified a G Suite domain:
//            //$domain = $payload['hd'];
//        } else {
//            // Invalid ID token
//        }
        dd($payload);
    }

}
