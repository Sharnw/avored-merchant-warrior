<?php

namespace Sharnw\AvoRedMerchantWarrior\API\Exceptions;

use Exception;

class InvalidConfigException extends Exception
{
    public function __construct()
    {
        $this->message = 'There was an error contacting the payment gateway. Please contact support.';
        $this->code = 500;
    }
}
