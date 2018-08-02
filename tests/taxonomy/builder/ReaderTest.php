<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 31-07-18
 * Time: 17:56
 */

namespace test\edwrodrig\ncbi\taxonomy\builder;

use edwrodrig\ncbi\taxonomy\builder\Reader;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class ReaderTest extends TestCase
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
    public function testHappyCase() {
        $dir =  $this->root->url() . '/test';
        mkdir($dir);
        file_put_contents($dir . '/names.dmp', '');
        file_put_contents($dir . '/nodes.dmp', '');

        $this->assertInstanceOf(Reader::class, new Reader($dir));
    }

    /**
     * @expectedException \edwrodrig\ncbi\taxonomy\builder\exception\FileNotFoundException
     * @expectedExceptionMessage not_existant_dir
     * @throws \edwrodrig\ncbi\taxonomy\builder\exception\FileNotFoundException
     */
    public function testNoDir() {
        $dir = $this->root->url() . '/not_existant_dir';

        $this->assertInstanceOf(Reader::class, new Reader($dir));
    }

    /**
     * @expectedException \edwrodrig\ncbi\taxonomy\builder\exception\FileNotFoundException
     * @expectedExceptionMessage names.dmp
     * @throws \edwrodrig\ncbi\taxonomy\builder\exception\FileNotFoundException
     */
    public function testNoNamesFile() {
        $dir = $this->root->url() . '/test';
        mkdir($dir);

        $this->assertInstanceOf(Reader::class, new Reader($dir));
    }

    /**
     * @expectedException \edwrodrig\ncbi\taxonomy\builder\exception\FileNotFoundException
     * @expectedExceptionMessage names.dmp
     * @throws \edwrodrig\ncbi\taxonomy\builder\exception\FileNotFoundException
     */
    public function testNoNodesFile() {
        $dir = $this->root->url() . '/test';
        mkdir($dir);
        file_put_contents($dir . '/nodes.dmp', '');

        $this->assertInstanceOf(Reader::class, new Reader($dir));
    }

    /**
     * @throws \edwrodrig\ncbi\taxonomy\builder\exception\FileNotFoundException
     */
    public function testHappyNodes() {
        $dir =  $this->root->url() . '/test';
        mkdir($dir);
        file_put_contents($dir . '/nodes.dmp', <<<EOF
1	|	1	|	no rank	|		|	8	|	0	|	1	|	0	|	0	|	0	|	0	|	0	|		|
2	|	131567	|	superkingdom	|		|	0	|	0	|	11	|	0	|	0	|	0	|	0	|	0	|		|
6	|	335928	|	genus	|		|	0	|	1	|	11	|	1	|	0	|	1	|	0	|	0	|		|
7	|	6	|	species	|	AC	|	0	|	1	|	11	|	1	|	0	|	1	|	1	|	0	|		|
9	|	32199	|	species	|	BA	|	0	|	1	|	11	|	1	|	0	|	1	|	1	|	0	|		|
10	|	1706371	|	genus	|		|	0	|	1	|	11	|	1	|	0	|	1	|	0	|	0	|		|
11	|	1707	|	species	|	CG	|	0	|	1	|	11	|	1	|	0	|	1	|	1	|	0	|		|

EOF
    );


        file_put_contents($dir . '/names.dmp', '');

        $reader = new Reader($dir);

        $nodes = iterator_to_array($reader->readNodes());

        $this->assertEquals([1 => 1, 2 => 131567, 6 => 335928, 7 => 6, 9 => 32199, 10 => 1706371, 11 => 1707], $nodes);

    }

    /**
     * @throws \edwrodrig\ncbi\taxonomy\builder\exception\FileNotFoundException
     */
    public function testHappyNames() {
        $dir =  $this->root->url() . '/test';
        mkdir($dir);
        file_put_contents($dir . '/names.dmp', <<<EOF
1	|	all	|		|	synonym	|
1	|	root	|		|	scientific name	|
2	|	Bacteria	|	Bacteria <prokaryotes>	|	scientific name	|
2	|	Monera	|	Monera <Bacteria>	|	in-part	|
2	|	Procaryotae	|	Procaryotae <Bacteria>	|	in-part	|
2	|	Prokaryota	|	Prokaryota <Bacteria>	|	in-part	|
2	|	Prokaryotae	|	Prokaryotae <Bacteria>	|	in-part	|
2	|	bacteria	|	bacteria <blast2>	|	blast name	|
2	|	eubacteria	|		|	genbank common name	|
2	|	not Bacteria Haeckel 1894	|		|	authority	|
2	|	prokaryote	|	prokaryote <Bacteria>	|	in-part	|
2	|	prokaryotes	|	prokaryotes <Bacteria>	|	in-part	|
6	|	Azorhizobium	|		|	scientific name	|
6	|	Azorhizobium Dreyfus et al. 1988 emend. Lang et al. 2013	|		|	authority	|
6	|	Azotirhizobium	|		|	misspelling	|
7	|	ATCC 43989	|		|	type material	|
7	|	Azorhizobium caulinodans	|		|	scientific name	|
7	|	Azorhizobium caulinodans Dreyfus et al. 1988	|		|	authority	|
7	|	Azotirhizobium caulinodans	|		|	equivalent name	|

EOF
        );


        file_put_contents($dir . '/nodes.dmp', '');

        $reader = new Reader($dir);

        $names = iterator_to_array($reader->readNames());

        $this->assertEquals([1 => 'root', 2 =>  'Bacteria', 6 => 'Azorhizobium', 7 => 'Azorhizobium caulinodans'], $names);

    }
}
