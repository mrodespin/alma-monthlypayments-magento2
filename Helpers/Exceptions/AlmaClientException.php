<?php

namespace Alma\MonthlyPayments\Helpers\Exceptions;

use Exception;

class AlmaClientException extends Exception
{
    /**
     * Get exception message same as Request error
     *
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->getMessage();
    }
}
