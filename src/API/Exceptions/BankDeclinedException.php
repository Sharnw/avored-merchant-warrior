<?php

namespace Sharnw\AvoRedMerchantWarrior\API\Exceptions;

use Exception;

class BankDeclinedException extends Exception
{
    public function __construct()
    {
        $this->message = 'Bank declined the transaction.';
        $this->code = 400;
    }
}
