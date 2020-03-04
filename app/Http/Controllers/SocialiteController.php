<?php

namespace App\Http\Controllers;

use App\Sheep;
use http\Env\Response;
use Illuminate\Http\Request;
use Socialite;
use Google_Client;
use \Facebook\Facebook as Facebook;
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



    public function FbWebAuth(){
        try {
            return Socialite::driver('facebook')->redirect();
        } catch (Exception $e) {
            // You should show something simple fail message
            return $this->sendFailedResponse($e->getMessage());
        }
    }


    public function FbWebAuthCallback(){
       $fb_user=Socialite::driver('facebook')->user();
       dd($fb_user);

    }


    public function FbCheckAndroidToken(Request $request)
    {
        $fb_token=$request['fb_token'];

        $FB_client=new Facebook ([
            'app_id' => '857041804747994',
            'app_secret' => '01b3b06a8f057b5ce7bc5c37dbbb2b00',
            'default_graph_version' => 'v5.0',    //  不要以爲安裝時寫 facebook/graph-sdk (5.7.0) ，就是 v5.7，是 v5.0啊！
            'default_access_token' => $fb_token,
        ]);
//        dd($FB_client);

        try{
            $fb_user =$FB_client ->get('/me?fields=id,name,email',$fb_token);

//            $fb_user = $FB_client -> get('/me',$fb_token);
        }catch(\Facebook\Exceptions\FacebookResponseException $e) {
            // Returns Graph API errors when they occur
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            // Returns SDK errors when validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }
        $fb_data = $fb_user->getGraphUser()->all();
//        dd($fb_data);

//        $fb_all=$fb_data->all();
        $fb_id = $fb_data['id'];
        $fb_name = $fb_data['name'];

        $fb_old_user = Sheep::where('fb_id',$fb_id)->first();
        $login_method = 'facebook';

        if($fb_old_user == null){

            $str_password = Str::random(20);
            $api_token = Str::random(13);
            $HashPwd = Hash::make($str_password);

            if(isset($fb_data['email'])){

                $fb_email = $fb_data['email'];
                $mail_create=Sheep::create([
                    'name' => $fb_name,
                    'email' => $fb_email,
                    'password' => $HashPwd,
                    'api_token' => $api_token,
                    'fb_id' => $fb_id,
                    'login_method' => $login_method,
                ]);

                    return response()->json([
                        'msg' => 'FB 使用者，新註冊，有 email',
                        'data' => $mail_create,

                ]);

            }else{

                $fb_id_create=Sheep::create([
                    'name' => $fb_name,
                    'password' => $HashPwd,
                    'api_token' => $api_token,
                    'fb_id' => $fb_id,
                    'login_method' => $login_method,
                ]);

                return response()->json([
                    'msg' => 'FB 使用者，新註冊，無 email',
                    'data' => $fb_id_create,
                    ]);
            }

        }else{

            $fb_up_token = Str::random(13);

            // 用 DB 方式 update
//            DB::table('sheep')
//                ->where('email',$google_email)
//                ->update(["api_token" =>$sheep_api_token]);

            $fb_old_user -> update(["api_token" =>$fb_up_token]);


            $new_fb_old_user = Sheep::where('fb_id',$fb_id)->first();


            return response()->json([
                'msg' => '此用戶已註冊過',
                'data' =>$new_fb_old_user,
            ]);
        }

    }
}
