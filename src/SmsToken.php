<?php

namespace Kineticamobile\SmsAuth;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Kineticamobile\SmsAuth\Events\SmsTokenWasCreated;
use Kineticamobile\SmsAuth\Events\SmsTokenWasVisited;
use Kineticamobile\SmsAuth\Events\SmsTokenWasSent;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * 
 * @property int $user_id
 * @property string $token
 * @property Carbon|null $available_at
 */
class SmsToken extends Model
{


    protected static function getTokenLength()
    {
        return config('sms-auth.token_length', 8);
    }

    /**
     * Create smstoken.
     *
     * @return self
     */
    public static function create(?int $user_id, ?int $lifetime = 60)
    {

        $smstoken = new static();
        $smstoken->user_id = $user_id;
        $smstoken->token = rand(pow(10, self::getTokenLength() - 1), pow(10, self::getTokenLength()) - 1);
        $smstoken->available_at = $lifetime
            ? Carbon::now()->addMinutes($lifetime)
            : null;


        $smstoken->save();

        Event::dispatch(new SmsTokenWasCreated($smstoken));

        return $smstoken;
    }

    /**
     * Call when smstoken has been used.
     *
     * @return void
     */
    public function visited()
    {
        Event::dispatch(new SmsTokenWasVisited($this));
        $this->delete();
    }

    /**
     * Get valid SmsToken by token.
     *
     * @param  string  $token
     * @return \SmsToken\SmsToken|null
     */
    public static function getValidSmsTokenByToken($user_id, $token)
    {
        return self::where('user_id', $user_id)
            ->where('token', $token)
            ->where(function ($query) {
                $query
                    ->whereNull('available_at')
                    ->orWhere('available_at', '>=', Carbon::now());
            })
            ->first();
    }
    /**
     * Get the user that owns the sms token.
     *
     * @param \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {

        return $this->belongsTo(config('sms-auth.user_model'));
    }



    /**
     * Get available SmsTokens by User.
     *
     * @param  string  $token
     * @return \SmsToken\SmsToken|null
     */
    public static function checkUserAvailableTokens($user_id)
    {
        return self::where('user_id', $user_id)
            ->where(function ($query) {
                $query
                    ->whereNull('available_at')
                    ->orWhere('available_at', '>=', Carbon::now());
            })
            ->count();
    }

    /**
     * Send smstoken.
     *
     * @return bool
     */
    public function send()
    {

        if (config('sms-auth.ubicual_token') == null) {
            return 'No se ha configurado el token de Ubicual';
        }
        $text = str_replace("%s", strval($this->token),config('sms-auth.sms_text', 'Código de acceso: %s'));
        $response = Http::get('https://api.ubicual.com/api/v2/sms/send?to=' . $this->user->phone . '&from='.config('sms-auth.sender').'&text=' . urlencode($text) . '&api_token=' . config('sms-auth.ubicual_token'));


        Event::dispatch(new SmsTokenWasSent($this));

        return true;
    }
}
