<?php

namespace Kineticamobile\SmsAuth\Events;

class SmsTokenWasCreated
{
    public $smstoken;

    public function __construct($smstoken)
    {
        $this->smstoken = $smstoken;
    }
}
