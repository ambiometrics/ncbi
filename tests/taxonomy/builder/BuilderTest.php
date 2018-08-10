<?php
declare(strict_types=1);


namespace test\edwrodrig\ncbi\taxonomy\builder;

use edwrodrig\ncbi\taxonomy\builder\Builder;
use edwrodrig\ncbi\taxonomy\builder\Reader;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PDO;
use PHPUnit\Framework\TestCase;
use test\edwrodrig\ncbi\Util;

class BuilderTest extends TestCase
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
     * @throws \edwrodrig\ncbi\taxonomy\builder\exception\FileNotFoundException
     * @throws \edwrodrig\ncbi\taxonomy\builder\exception\BuildTargetAlreadyExistsException
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
        $builder->setTargetFilename($this->db_name);
        $builder->build();

        $this->assertFileExists($builder->getTargetFilename());


        $db = new PDO('sqlite:' . $builder->getTargetFilename());
        $result = $db->query('SELECT count(*) FROM nodes');
        $this->assertEquals(3, $result->fetchColumn(0));
        $result = $db->query('SELECT count(*) FROM names');
        $this->assertEquals(3, $result->fetchColumn(0));
    }

}
