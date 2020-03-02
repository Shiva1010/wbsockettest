<?php

namespace App\Http\Controllers;

use App\Sheep;
use http\Env\Response;
use Illuminate\Http\Request;
use Socialite;
use Google_Client;
use Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use DB;



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
        $name=$user->name;
        $email=$user->email;
        $token=$user->token;

        $google_user = Sheep::where('email',$email)->first();

        if($google_user == null )
        {
            $str_pd = Str::random(15);
            $api_token = Str::random(12);
            $hash_pd = Hash::make($str_pd);
            $login_method = 'google_web';


            $create = Sheep::create([

                'name' => $name,
                'email' => $email,
                'password' => $hash_pd,
                'api_token' => $api_token,
                'login_method' => $login_method

            ]);

            return response()->json([
                'msg' => '此帳號尚未註冊，現在將幫你進行註冊',
                'data' => $create
            ]);
        }else{


            $new_api_token = Str::random(15);


            $google_user -> update(["api_token" =>$new_api_token]);


            $new_google_user = Sheep::where('email',$email)->first();




            return response()->json([
                'msg' => '此用戶已註冊過',
                'data' =>$new_google_user,
            ]);


        }
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


    public function FirebaseCheckAndroidToken(Request $request)
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

//        dd($payload);
        $google_email = $payload ["email"];
        $google_name = $payload ["name"];

        $sheep_data = Sheep::where('email',$google_email)->first();

        if ($sheep_data == null)
        {
            $str_password = Str::random(20);
            $api_token = Str::random(15);
            $HashPwd = Hash::make($str_password);
            $login_method = 'google';

            $create = Sheep::create([
                'name' => $google_name,
                'email' => $google_email,
                'password' => $HashPwd,
                'api_token' => $api_token,
                'login_method' => $login_method,
            ]);

            return response()->json([
                'msg' => '尚未註冊，新註冊戶',
                'data' => $create
            ]);

        }else{



            $sheep_api_token = Str::random(15);

            // 用 DB 方式 update
//            DB::table('sheep')
//                ->where('email',$google_email)
//                ->update(["api_token" =>$sheep_api_token]);

            $sheep_data -> update(["api_token" =>$sheep_api_token]);


            $new_sheep_data = Sheep::where('email',$google_email)->first();




            return response()->json([
                'msg' => '此用戶已註冊過',
                'data' =>$new_sheep_data,
            ]);
        }


    }

}
