<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 03-08-18
 * Time: 9:50
 */

namespace test\edwrodrig\ncbi\taxonomy;

use edwrodrig\ncbi\taxonomy\builder\Builder;
use edwrodrig\ncbi\taxonomy\builder\Reader;
use edwrodrig\ncbi\taxonomy\Dao;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use test\edwrodrig\ncbi\Util;

class DaoTest extends TestCase
{

    /**
     * @throws \edwrodrig\ncbi\taxonomy\builder\exception\BuildTargetAlreadyExistsException
     * @throws \edwrodrig\ncbi\taxonomy\builder\exception\FileNotFoundException
     */
    public function testGetParentIdNodeByIdNode()
    {
        $database = __DIR__ . '/../files/database/db.sqlite3';


        $service = new Dao($database);

        $this->assertEquals(2, $service->getParentIdNodeByIdNode(3));
        $this->assertEquals(1, $service->getParentIdNodeByIdNode(2));
        $this->assertEquals(1, $service->getParentIdNodeByIdNode(1));
        $this->assertNull($service->getParentIdNodeByIdNode(4));
    }

    public function testGetNameByIdNode()
    {
        $database = __DIR__ . '/../files/database/db.sqlite3';


        $service = new Dao($database);

        $this->assertEquals('A', $service->getScientificNameByIdNode(1));
        $this->assertEquals('B', $service->getScientificNameByIdNode(2));
        $this->assertEquals('C', $service->getScientificNameByIdNode(3));
        $this->assertNull($service->getParentIdNodeByIdNode(4));
    }

    public function testValidate()
    {
        $database = __DIR__ . '/../files/database/db.sqlite3';

        $service = new Dao($database);

        $this->assertTrue($service->validate());
    }
}
