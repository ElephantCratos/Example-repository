<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\TwoAuthCode;
use App\Models\User;

use App\Mail\AuthSender;
use Illuminate\Support\Facades\Mail;

use Carbon\Carbon;

class TwoFactorAuthController extends Controller
{
    public static function sendEmailVerification(?User $user = null)
    {
       if ($user == null) {
        $user = Auth::user();
       }

        $code = self::createNewCode($user);
        Mail::to($user->email)->send(new AuthSender($user->username, $code));

        return response()->json([
        'message' => 'Новый код был отправлен на почту']);
    }

    public static function createNewCode(User $user)
    {
        
        $userCodesCollection = TwoAuthCode::where('user_id', $user->id)
        ->where('expired_at', '>', Carbon::now())
        ->orderByDesc('expired_at');
        
       
        if ($userCodesCollection->count() > 3){
            if ($userCodesCollection->first()->expired_at){
                
                $time = Carbon::now()->addMinutes(env('TWO_FACTOR_AUTH_EXPIRATION_TIME', 15));
                $time = (int)$time->format('U');

                $latestExpiredAt = $userCodesCollection->first()->expired_at;
                $latestExpiredAtCarbon = Carbon::createFromFormat('Y-m-d H:i:s', $latestExpiredAt);
                $latestExpiredAtCarbon = (int)$latestExpiredAtCarbon->format('U');

                if ($time - $latestExpiredAtCarbon < 30) {
                    return abort(429, 'Подождите некоторое время, прежде чем попросить код снова');
                }
            }
        }

        $code = strval(mt_rand(100000, 999999));
    
  
        $codeModel = new TwoAuthCode([
            'code' => Hash::make($code),
            'expired_at' => Carbon::now()->addMinutes(env('TWO_FACTOR_AUTH_EXPIRATION_TIME', 15)),
            'isValid' => true,
            'user_id'=> $user->id
            ]);

        $codeModel->save();
        return $code;
    }

    public static function confirmCode($codeToApprove)
    {
        
        $user = Auth::user();
        $requiredCode = TwoAuthCode::where('expired_at', '>', Carbon::now())
        ->where('user_id', '=' , $user->id) -> first();
        
         if (Hash::check($codeToApprove, $requiredCode->code)){
            $requiredCode->delete(); 
            return true; 
        } 
        return false; 
    }
}




