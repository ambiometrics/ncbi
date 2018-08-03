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
use edwrodrig\ncbi\taxonomy\Service;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use test\edwrodrig\ncbi\Util;

class ServiceTest extends TestCase
{

    /**
     * @var vfsStreamDirectory
     */
    private $root;

    /**
     * @var string
     */
    private $db_name;

    public function setUp() {
        $this->root = vfsStream::setup();
        $this->db_name = tempnam(sys_get_temp_dir(), 'TEST_SQL');
        if ( file_exists($this->db_name))
            unlink($this->db_name);
    }

    public function tearDown() {
        if ( file_exists($this->db_name))
            unlink($this->db_name);
    }

    /**
     * @throws \edwrodrig\ncbi\taxonomy\builder\exception\BuildTargetAlreadyExistsException
     * @throws \edwrodrig\ncbi\taxonomy\builder\exception\FileNotFoundException
     */
    public function testGetParentIdNodeByIdNode()
    {
        $folder = $this->root->url() . '/test';

        Util::createFiles( $folder, [
            1 => ['parent_id' => 1, 'name' => 'A'],
            2 => ['parent_id' => 1, 'name' => 'B'],
            3 => ['parent_id' => 2, 'name' => 'C']
        ]);
        $reader = new Reader($folder);
        $builder = new Builder($reader);
        $builder->setTarget($this->db_name);
        $builder->build();

        $service = new Service($builder->getTarget());

        $this->assertEquals(2, $service->getParentIdNodeByIdNode(3));
        $this->assertEquals(1, $service->getParentIdNodeByIdNode(2));
        $this->assertEquals(1, $service->getParentIdNodeByIdNode(1));
        $this->assertNull($service->getParentIdNodeByIdNode(4));
    }

    public function testGetNameByIdNode()
    {
        $folder = $this->root->url() . '/test';

        Util::createFiles( $folder, [
            1 => ['parent_id' => 1, 'name' => 'A'],
            2 => ['parent_id' => 1, 'name' => 'B'],
            3 => ['parent_id' => 2, 'name' => 'C']
        ]);
        $reader = new Reader($folder);
        $builder = new Builder($reader);
        $builder->setTarget($this->db_name);
        $builder->build();

        $service = new Service($builder->getTarget());

        $this->assertEquals('A', $service->getNameByIdNode(1));
        $this->assertEquals('B', $service->getNameByIdNode(2));
        $this->assertEquals('C', $service->getNameByIdNode(3));
        $this->assertNull($service->getParentIdNodeByIdNode(4));
    }
}
