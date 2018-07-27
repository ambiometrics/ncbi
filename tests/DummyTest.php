<?php
declare(strict_types=1);

namespace test\edwrodrig\ncbi;

use edwrodrig\ncbi\Dummy;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

class DummyTest extends TestCase
{

    /**
     * @var vfsStreamDirectory
     */
    private $root;

    public function setUp() {
        $this->root = vfsStream::setup();
    }

    public function testHappyCase() {
        $filename =  $this->root->url() . '/test';

        file_put_contents($filename, <<<EOF
LINE1
LINE2
LINE3
EOF
        );

	$this->assertEquals(3, Dummy::sum(1,2));
    }
}
