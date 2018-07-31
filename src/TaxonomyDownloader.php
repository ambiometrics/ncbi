<?php
declare(strict_types=1);

namespace edwrodrig\ncbi;

use ZipArchive;

class TaxonomyDownloader {

    const FILE = "ftp://ftp.ncbi.nlm.nih.gov/pub/taxonomy/taxdmp.zip";

    public $downloaded_file = '/home/edwin/taxdmp.zip';

    public $nodes = [];

    public $root = null;


	public function download() {
		copy(self::FILE, $this->downloaded_file);
	}

	public function unzip() {

        $this->temp_dir = tempnam(sys_get_temp_dir(), 'Tux');
        unlink($this->temp_dir);



	    $zip = new ZipArchive;
	    $zip->open($this->downloaded_file);
	    mkdir($this->temp_dir);
	    $zip->extractTo($this->temp_dir, ['nodes.dmp', 'names.dmp']);
    }

    public function readNames() {
	    $file = fopen($this->temp_dir . '/names.dmp', 'r');
	    while ( $line = fgets($file) ) {
	        $tokens = explode("|", $line);

	        $type = trim($tokens[3]);
	        if ( $type != 'scientific name') continue;

	        $id = trim($tokens[0]);
	        $name = trim($tokens[1]);

	        yield $id => $name;
        }

	    fclose($file);
    }

    public function readNodes() {
        $file = fopen($this->temp_dir . '/nodes.dmp', 'r');

        while ( $line = fgets($file) ) {
            $tokens = explode("|", $line);

            $id = trim($tokens[0]);
            $parent = trim($tokens[1]);

            yield $id => $parent;
        }

        fclose($file);
    }



}
