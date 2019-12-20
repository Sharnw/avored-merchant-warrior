<?php

namespace Sharnw\AvoRedMerchantWarrior\API\Exceptions;

use Exception;

class ApiException extends Exception
{
    public function __construct($message = null)
    {
        $this->message = $message ?? 'Payment attempt failed.';
        $this->code = 400;
    }
}
