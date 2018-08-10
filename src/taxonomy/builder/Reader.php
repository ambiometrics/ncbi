<?php
declare(strict_types=1);

namespace edwrodrig\ncbi\taxonomy\builder;

/**
 * Class Reader
 *
 * Class to read files with the format of taxanomic data of NCBI.
 * To get the files use the {@see Downloader}
 * @package edwrodrig\ncbi\taxonomy\builder
 */
class Reader
{

    /**
     * The folder where the files are extracted.
     * @var string
     */
    private $folder;


    /**
     * Reader constructor.
     *
     * This validates that the folder exists and have the needed files.
     * The folder must containt the {@see Reader::getNamesFilename() names.dmp} and {@see Reader::getNodesFilename() nodes.dmp} file
     * @param string $folder
     * @see Reader::$folder
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
     * Get the filename of the file contains the parent info of the nodes
     * The path contains the {@see Reader::$folder folder}
     * @return string
     */
    private function getNodesFilename() : string {
        return $this->folder . '/nodes.dmp';
    }

    /**
     * Read the names
     *
     * This is an iterable method that returns in every element the id as a key and the name as the value.
     * Only the rows that are scientific names are considered.
     * Scientific names rows are those which have `scientific name` in the fourth column.
     * The id corresponds the first column of the {@see Reader::getNammesFilename() file}.
     * The name corresponds the second column of the {@see Reader::getNamesFilename() file}.
     * ```
     * foreach ( $this->readNames() as $id => $name ) {
     * }
     * ```
     */
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

    /**
     * Read the nodes
     *
     * This is an iterable method that returns in every element the id as a key and the parent id as the value
     * The id corresponds the first column of the {@see Reader::getNodesFilename() file}.
     * The parent id corresponds the second column of the {@see Reader::getNodesFilename() file}.
     * ```
     * foreach ( $this->readNodes() as $id => $parent_id ) {
     * }
     * ```
     * @return \Generator
     */
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