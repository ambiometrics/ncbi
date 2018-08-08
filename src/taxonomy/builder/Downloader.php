<?php
declare(strict_types=1);

namespace edwrodrig\ncbi\taxonomy\builder;

use ZipArchive;

/**
 * Class Downloader
 *
 * This class downloads the files to build the taxonomy database
 * @package edwrodrig\ncbi\taxonomy\builder
 */
class Downloader {

    const FILE = "ftp://ftp.ncbi.nlm.nih.gov/pub/taxonomy/taxdmp.zip";

    /**
     * @var string
     */
    private $downloaded_file;

    /**
     * @var string
     */
    private $output_dir;

    public function __construct() {
        $this->downloaded_file = tempnam(sys_get_temp_dir(), 'tax_dl_');
        $this->output_dir = tempnam(sys_get_temp_dir(), 'tax_dl_unzip_');
    }

    /**
     * Download the file
     */
	public function download() {
		copy(self::FILE, $this->downloaded_file);
	}

    /**
     * Unzip
     */
	public function unzip() {
        unlink($this->output_dir);

	    $zip = new ZipArchive;
	    $zip->open($this->downloaded_file);
	    mkdir($this->output_dir);
	    $zip->extractTo($this->output_dir, ['nodes.dmp', 'names.dmp']);
    }

    /**
     * Get a data database builder
     * @throws exception\FileNotFoundException
     */
    public function getBuilder() : Builder {
        $reader = new Reader($this->output_dir);
        return new Builder($reader);
    }
}
