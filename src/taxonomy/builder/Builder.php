<?php
declare(strict_types=1);

namespace edwrodrig\ncbi\taxonomy\builder;

use edwrodrig\ncbi\taxonomy\builder\exception\BuildTargetAlreadyExistsException;
use PDO;

/**
 * Class Builder
 *
 * This class build a  taxonomy database from taxononic files downloaded with {@see Downloader the downloader}
 * The downloader has a {@see Downloader::getBuilder() method to create a builder from downloaded files}
 * Only create a builder class when you're trying to do advanced stuff.
 * ```
 * $builder = $downloader->getBuilder();
 * $builder->setTargetFilename('/path/to/database.sqlite3');
 * $builder->build();
 * ```
 * When the database is builder the files from the {@see Reader reader} are not longer needed
 *
 * @package edwrodrig\ncbi\taxonomy\builder
 */
class Builder
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * The target filename is the name where the sqlite3 database was saved.
     * @var string|null
     */
    private $target_filename = null;

    /**
     * Operations per commmit
     * @var int
     */
    private $commit_size = 500;

    public function __construct(Reader $reader) {
        $this->reader = $reader;
    }

    /**
     * Get the target filename database.
     * If must be set or this method will fail.
     * @return string
     */
    public function getTargetFilename() : string {
        return $this->target_filename;
    }

    /**
     * Set the database target filename.
     * This is needed for the builder to {@see Builder::build() work correctly}.
     *
     * @param string $target_filename
     * @return Builder
     */
    public function setTargetFilename(string $target_filename) : Builder {
        $this->target_filename = $target_filename;
        return $this;
    }

    /**
     * Build the taxonomy database.
     *
     * The target filename must be {@see Builder::setTargetFilename() set} before this method is called or thi will fail.
     *
     */
    public function build() : Builder {

        if ( file_exists($this->target_filename) ) {
            throw new BuildTargetAlreadyExistsException($this->target_filename);
        }

        $db = new PDO('sqlite:' . $this->target_filename);
        $db->exec('CREATE TABLE names (id INTEGER, name TEXT)');
        $db->exec('CREATE TABLE nodes (id INTEGER, parent_id INTEGER)');

        $operations = 0;
        $db->beginTransaction();
        foreach ( $this->reader->readNames() as $id => $name ) {
            $stmt = $db->prepare('INSERT INTO names (id, name) VALUES(?,?)');
            $stmt->bindValue(1, $id, PDO::PARAM_INT);
            $stmt->bindValue(2, $name, PDO::PARAM_STR);
            $stmt->execute();

            if ( ++$operations > $this->commit_size ) {
                $operations = 0;
                $db->commit();
                $db->beginTransaction();
            }

        }
        $db->commit();

        $operations = 0;
        $db->beginTransaction();
        foreach ( $this->reader->readNodes() as $id => $parentId ) {
            $stmt = $db->prepare('INSERT INTO nodes (id, parent_id) VALUES(?,?)');
            $stmt->bindValue(1, $id, PDO::PARAM_INT);
            $stmt->bindValue(2, $parentId, PDO::PARAM_INT);
            $stmt->execute();

            if ( ++$operations > $this->commit_size ) {
                $operations = 0;
                $db->commit();
                $db->beginTransaction();
            }
        }
        $db->commit();

        $db->exec('CREATE UNIQUE INDEX idx_names_id ON names(id)');
        $db->exec('CREATE UNIQUE INDEX idx_nodes_id ON nodes(id)');
        $db->exec('CREATE UNIQUE INDEX idx_nodes_parent_id ON nodes(parent_id)');

        return $this;
    }

}