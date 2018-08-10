<?php
declare(strict_types=1);

namespace edwrodrig\ncbi\taxonomy\builder;

use ZipArchive;

/**
 * Class Downloader
 *
 * This class downloads the files to build the taxonomy database
 * Use this in the following way:
 * ```
 * $downloader = new Downloader();
 * $downloader->download();
 * $downloader->unzip();
 *
 * $builder = $downloader->getBuilder();
 * ```
 *
 * @package edwrodrig\ncbi\taxonomy\builder
 */
class Downloader {

    /**
     * The url where the file is located
     */
    const FILE = "ftp://ftp.ncbi.nlm.nih.gov/pub/taxonomy/taxdmp.zip";

    /**
     * @var string|null
     */
    private $downloaded_filename = null;

    /**
     * @var string|null
     */
    private $output_dir = null;

    /**
     * Get the downloaded file name
     *
     * This is a zip file with the taxonomic data of the ncbi.
     * It's about 50M size
     * @return string
     */
    public function getDownloadedFilename() : string {
        return $this->downloaded_filename;
    }

    /**
     * Get there the output dir is extracted.
     * It should have  the names.dmp and nodes.dmp
     * @return string
     */
    public function getOutputDir() : string {
        return $this->output_dir;
    }

    /**
     * Download the file.
     *
     * To get the downloaded filename use {@see Downloader::getDownloadedFilename()}
     */
	public function download() : Downloader {
        $this->downloaded_filename = tempnam(sys_get_temp_dir(), 'tax_dl_');
		copy(self::FILE, $this->downloaded_filename);
		return $this;
	}

    /**
     * Unzip the downloaded file.
     *
     * Unzip the downloaded file to an output directory.
     * It just extract 2 files nodes.dmp and names.dmp.
     * Cleaning this files is up to you.
     * @see Downloader::getOutputDir() to get the output dir
     * @see Downloader::getBuilder() to get a database builder using the output dir
     *
     */
	public function unzip() : Downloader {
	    if ( is_null($this->downloaded_filename) || !file_exists($this->downloaded_filename) )
	        throw new exception\FileNotFoundException($this->downloaded_filename);


	    $zip = new ZipArchive;
	    $zip->open($this->downloaded_filename);

        $this->output_dir = tempnam(sys_get_temp_dir(), 'tax_dl_unzip_');
        unlink($this->output_dir);
	    mkdir($this->output_dir);

	    $zip->extractTo($this->output_dir, ['nodes.dmp', 'names.dmp']);

	    return $this;
    }

    /**
     * Get a data database builder.
     *
     * The builder is an object that builds a taxonomic info database from the raw taxonomic data contained in the {@see Downloader::getOutputDir() output dir}
     *
     * @throws exception\FileNotFoundException
     */
    public function getBuilder() : Builder {
        $reader = new Reader($this->output_dir);
        return new Builder($reader);
    }
}
