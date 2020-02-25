<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Sheep;
use App\SocialSheep;
use App;
use Auth;
use Config;
use Redirect;
use Socialite;

class SocialController extends Controller
{
    Public function getSocialRedirect($provider){
        $providerkey = Config::get('services.' . $provider);
        if (empty($providerkey)){
            return App::abort(404);
        }

        return SocialSheep::driver($provider)->redirect();

    }

    Public function  getSocialCallback($provider, Request $request){
        if ($request -> exists('error_code')){
            return Redirect::action('Auth\LoginController@showLoginForm')
                //  這裡的 action 應該需要做更動
            ->withErrors([

                'msg' => $provider . '登入失敗或綁定失敗，請重新再試'

                ]);

            $socialite_user = Socialte::with($provider) -> user();

            $login_sheep = null;

            $s_s = Sheep::where('provider_sheep_id',$socialite_user -> id)
                ->where('provider',$provider)
                ->first();
            // 查看是否已有其他使用者使用此id ， 但應該可以用 email 處理

            if(!empty($s_s)){
                $login_sheep = $s_s->user;
            }else{
                if (empty($socialite_user->email)){
                    return Redirect::action('Auth\LoginController@showLoginForm')
                        //  待改：這裡的 action 應該需要做更動
                    ->withErrors([
                        'msg' => '很抱歉，我們無法從你的' . $provider . '帳號抓到信箱，請用其他方式註冊帳號，謝謝！'
                        ]);
                }
            }

            $sheep = Sheep::where('email', $socialite_user->email) -> first();

            if(!empty ($sheep)){
                $login_sheep = $sheep;

                $s_sheep = $login_sheep -> socialUser;
                // 待改： SocialUser 是什麼

                if(!empty($s_sheep)){
                    return Redirect::action('Auth\LoginController@showLoginForm')
                       //  待改：這裡的 action 應該需要做更動
                    ->withErrors([
                        'msg' => '此 email 已被其他帳號綁定了，請使用其他登入方式'
                       ]);

                }else{
                    $login_sheep->socialSheep=SocialSheep::create([
                        'provider_sheep_id' => $socialite_user -> id,
                        'provider' => $provider,
                        'sheep_id' => $login_sheep -> id,
                    ]);
                }

            }else{
                $login_sheep = Shepp::create([
                    'email' => $socialite_user ->email,
                    'password' => bcrypt(str_random(8)),
                    'name' => $socialite_user->name,

                ]);

                $login_sheep->sociaUser = SocialSheep::create([
                    'provider_sheep_id' => $socialite_user->id,
                    'provider' => $provider,
                    'sheep_id' => $login_user->id,
                ]);
            }

            if(!is_null($login_sheep)){
                Auth::login($login_sheep);
                return Redirect::action('Homecontroller@index');
            }

            return App::abort(500);

        }
    }
}


