<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 09-08-18
 * Time: 11:02
 */

namespace edwrodrig\ncbi\exception;


use Exception;

class InvalidActionException extends Exception
{

    /**
     * InvalidActionException constructor.
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
    }
}