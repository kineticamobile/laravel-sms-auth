<?php

namespace Kineticamobile\SmsAuth\Events;

class SmsTokenWasSent
{
    public $smstoken;

    public function __construct($smstoken)
    {
        $this->smstoken = $smstoken;
    }
}
