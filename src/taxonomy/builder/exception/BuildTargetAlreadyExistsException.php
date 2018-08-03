<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 03-08-18
 * Time: 9:58
 */

namespace edwrodrig\ncbi\taxonomy\builder\exception;


use Exception;

class BuildTargetAlreadyExistsException extends Exception
{

    /**
     * BuildTargetAlreadyExistsException constructor.
     * @param string $target
     */
    public function __construct(string $target)
    {
        parent::__construct($target);
    }
}