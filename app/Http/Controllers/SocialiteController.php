<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Socialite;

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

    public function HandleProviderCallback(Request $request)
    {
//        $sheep_email = $request['email'];
        $token = $request['token'];

        $user =Socialite::driver('google')->userFromToken($token);

        dd($user);
    }

}
