<?php
declare(strict_types=1);

namespace edwrodrig\ncbi\taxonomy\builder;

/**
 * Class Reader
 *
 * Class
 * @package edwrodrig\ncbi\taxonomy\builder
 */
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

    /**
     * Get the names filename
     *
     * Get the filename of the file containing the name info of the nodes.
     * The path contains the {@see Reader::$folder folder}
     * @return string
     */
    private function getNamesFilename() : string {
        return $this->folder . '/names.dmp';
    }

    /**
     * Get the node filename
     *
     * Get the filename of the file containgin the parent info of the nodes
     * The path contains the {@see Reader::$folder folder}
     * @return string
     */
    private function getNodesFilename() : string {
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