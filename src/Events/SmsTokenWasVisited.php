<?php

namespace Kineticamobile\SmsAuth\Events;

class SmsTokenWasVisited
{
    public $smstoken;

    public function __construct($smstoken)
    {
        $this->smstoken = $smstoken;
    }
}
