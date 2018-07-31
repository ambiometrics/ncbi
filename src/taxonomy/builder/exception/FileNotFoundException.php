<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 31-07-18
 * Time: 17:46
 */

namespace edwrodrig\ncbi\taxonomy\builder\exception;

use Exception;

class FileNotFoundException extends Exception
{

    /**
     * FileNotFoundException constructor.
     * @param string $folder
     */
    public function __construct(string $folder)
    {
        parent::__construct($folder);
    }
}