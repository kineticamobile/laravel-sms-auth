<?php

namespace Kineticamobile\SmsAuth\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Kineticamobile\SmsAuth\SmsToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class SmsAuthController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    public function sms_auth()
    {
        if(session()->has('sms_auth_user')) {
            session()->forget('sms_auth_user');
        }
        if (Auth::check()) {
            return redirect(config('sms-auth.redirect_route', '/'));
        }
        
        return view('sms-auth::sms_auth');
    }

    public function sms_login(Request $request)
    {   
        $user = config('sms-auth.user_model')::where('phone', $request->phone)->first();

        if(!$user) {
            return redirect()->back()->withErrors(['Usuario no encontrado']);
        }
        session(['sms_auth_user' => $user->id]);


        if(!SmsToken::checkUserAvailableTokens($user->id)>0){
            $sms_token=SmsToken::create($user->id,config('sms-auth.token_lifetime',60));
        }else{
            return redirect()->back()->withErrors(['Debe esperar al menos '.config('sms-auth.token_lifetime',60).' minuto/s para solicitar otro código']);
        }
        try {
            $response=$sms_token->send();
            if(is_bool($response) && $response == true){
                return view('sms-auth::sms_verify');
            }else{
                return redirect()->back()->withErrors(['error' => config('sms-auth.send_error','Se ha producido un error al enviar el código. Contacte con el administrador')]);
            }
        } catch (\Throwable $th) {
            Log::error("message: ".$th->getMessage()."\n".$th->getTraceAsString());
            return redirect()->back()->withErrors(['error' => config('sms-auth.send_error','Se ha producido un error al enviar el código. Contacte con el administrador')]);
        }
    }

    public function sms_verify(Request $request)
    {
        if (session()->has('sms_auth_user') && $request->token) {
            $sms_token = SmsToken::where('user_id', session('sms_auth_user'))->where('token', $request->token)->first();
            if ($sms_token) {
                auth()->login($sms_token->user); 
                $sms_token->delete();

                // Auth::guard('web')->loginUsingId($sms_token->user_id);

                return redirect(config('sms-auth.redirect_route', '/'));
            }
        }
        return view('sms-auth::sms_verify')->withErrors(['error' => 'Invalid token']);
    }
}
