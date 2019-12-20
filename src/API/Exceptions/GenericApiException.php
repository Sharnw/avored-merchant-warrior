<?php

namespace Sharnw\AvoRedMerchantWarrior\API\Exceptions;

use Exception;

class GenericApiException extends Exception
{
    public function __construct()
    {
        $this->message = 'There was an error contacting the payment gateway. Please try again later.';
        $this->code = 400;
    }
}
