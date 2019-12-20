<?php

namespace Sharnw\AvoRedMerchantWarrior\API\Exceptions;

use Exception;

class BankErrorException extends Exception
{
    public function __construct()
    {
        $this->message = 'Transaction failed. Please contact your bank.';
        $this->code = 400;
    }
}
