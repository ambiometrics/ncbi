<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 31-07-18
 * Time: 17:23
 */

namespace edwrodrig\ncbi\taxonomy\builder;


class Reader
{

    /**
     * @var string
     */
    private $folder;


    /**
     * Reader constructor.
     * @param string $folder
     * @throws exception\FileNotFoundException
     */
    public function __construct(string $folder) {
        if ( !is_dir($folder) ) {
                throw new exception\FileNotFoundException($folder);
        }

        $this->folder = $folder;

        if ( !file_exists($this->getNamesFilename()) ) {
            throw new exception\FileNotFoundException($this->getNamesFilename());
        }

        if ( !file_exists($this->getNodesFilename()) ) {
            throw new exception\FileNotFoundException($this->getNamesFilename());
        }
    }


    public function getNamesFilename() : string {
        return $this->folder . '/names.dmp';
    }

    public function getNodesFilename() : string {
        return $this->folder . '/nodes.dmp';
    }

    public function readNames() {
        $file = fopen($this->getNamesFilename(), 'r');
        while ($line = fgets($file)) {
            $tokens = explode("|", $line);

            $type = trim($tokens[3]);
            if ($type != 'scientific name') continue;

            $id = trim($tokens[0]);
            $name = trim($tokens[1]);

            yield $id => $name;
        }
        fclose($file);

    }

    public function readNodes() {

        $file = fopen($this->getNodesFilename(), 'r');

        while ($line = fgets($file)) {
            $tokens = explode("|", $line);

            $id = trim($tokens[0]);
            $parent = trim($tokens[1]);

            yield $id => $parent;
        }

        fclose($file);
    }
}