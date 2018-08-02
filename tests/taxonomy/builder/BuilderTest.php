<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 01-08-18
 * Time: 17:10
 */

namespace test\edwrodrig\ncbi\taxonomy\builder;

use edwrodrig\ncbi\taxonomy\builder\Builder;
use edwrodrig\ncbi\taxonomy\builder\Reader;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PDO;
use PHPUnit\Framework\TestCase;
use SQLite3;
use test\edwrodrig\ncbi\Util;

class BuilderTest extends TestCase
{

    /**
     * @var vfsStreamDirectory
     */
    private $root;

    public function setUp() {
        $this->root = vfsStream::setup();
    }

    /**
     * @throws \edwrodrig\ncbi\taxonomy\builder\exception\FileNotFoundException
     */
    public function testBuild()
    {
        $folder = $this->root->url() . '/test';

        Util::createFiles( $folder, [
            1 => ['parent_id' => 1, 'name' => 'A'],
            2 => ['parent_id' => 1, 'name' => 'B'],
            3 => ['parent_id' => 1, 'name' => 'C']
        ]);
        $reader = new Reader($folder);
        $builder = new Builder($reader);
        $builder->build();

        $this->assertFileExists($builder->getTarget());
        $this->assertTrue($builder->validate());

        $db = new PDO('sqlite:' . $builder->getTarget());
        $result = $db->query('SELECT count(*) FROM nodes');
        $this->assertEquals(3, $result->fetchColumn(0));
        $result = $db->query('SELECT count(*) FROM names');
        $this->assertEquals(3, $result->fetchColumn(0));
    }
}
