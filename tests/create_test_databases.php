<?php

use test\edwrodrig\ncbi\Util;

include_once __DIR__ . '/../vendor/autoload.php';

Util::createTempDatabase(__DIR__ . '/files/database/db.sqlite3', [
    1 => ['parent_id' => 1, 'name' => 'A'],
    2 => ['parent_id' => 1, 'name' => 'B'],
    3 => ['parent_id' => 2, 'name' => 'C']
]);