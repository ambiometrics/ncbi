<?php
declare(strict_types=1);

namespace edwrodrig\ncbi\taxonomy\exception;

use Exception;

class DaoValidationException extends Exception
{

    /**
     * DaoValidationException constructor.
     * @param string $values
     */
    public function __construct(string $values)
    {
        parent::__construct($values);
    }
}