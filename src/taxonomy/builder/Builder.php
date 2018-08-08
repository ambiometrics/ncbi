<?php
declare(strict_types=1);

namespace edwrodrig\ncbi\taxonomy\builder;

use PDO;

/**
 * Class Builder
 * @package edwrodrig\ncbi\taxonomy\builder
 */
class Builder
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var string
     */
    private $target;

    public function __construct(Reader $reader) {
        $this->reader = $reader;
        $this->target = tempnam(sys_get_temp_dir(), 'tax_db_');
        unlink($this->target);
    }

    /**
     * @return string
     */
    public function getTarget() : string {
        return $this->target;
    }

    public function setTarget(string $target) : string {
        return $this->target = $target;
    }

    public function build() {

        if ( file_exists($this->target) ) {
            throw new exception\BuildTargetAlreadyExistsException($this->target);
        }

        $db = new PDO('sqlite:' . $this->target);
        $db->exec('CREATE TABLE names (id INTEGER, name TEXT)');
        $db->exec('CREATE TABLE nodes (id INTEGER, parent_id INTEGER)');


        $i = 0;
        $db->beginTransaction();
        foreach ( $this->reader->readNames() as $id => $name ) {
            $stmt = $db->prepare('INSERT INTO names (id, name) VALUES(?,?)');
            $stmt->bindValue(1, $id, PDO::PARAM_INT);
            $stmt->bindValue(2, $name, PDO::PARAM_STR);
            $stmt->execute();

            if ( ++$i > 500 ) {
                $i = 0;
                $db->commit();
                $db->beginTransaction();
            }

        }
        $db->commit();

        $i = 0;
        $db->beginTransaction();
        foreach ( $this->reader->readNodes() as $id => $parentId ) {
            $stmt = $db->prepare('INSERT INTO nodes (id, parent_id) VALUES(?,?)');
            $stmt->bindValue(1, $id, PDO::PARAM_INT);
            $stmt->bindValue(2, $parentId, PDO::PARAM_INT);
            $stmt->execute();

            if ( ++$i > 500 ) {
                $i = 0;
                $db->commit();
                $db->beginTransaction();
            }
        }
        $db->commit();

        $db->exec('CREATE UNIQUE INDEX idx_names_id ON names(id)');
        $db->exec('CREATE UNIQUE INDEX idx_nodes_id ON nodes(id)');
        $db->exec('CREATE UNIQUE INDEX idx_nodes_parent_id ON nodes(parent_id)');
    }

    public function validate() : bool {
        $db = new PDO('sqlite:' . $this->target);
        $result = $db->query('SELECT name FROM sqlite_master WHERE type = "table"');
        $tables = $result->fetchAll(PDO::FETCH_COLUMN);
        if ( $tables != ['names', 'nodes'])
            throw new \Exception(print_r($tables, true));

        $result = $db->query('SELECT name FROM sqlite_master WHERE type = "index"');
        $indexes = $result->fetchAll(PDO::FETCH_COLUMN);
        if ( $indexes != ['idx_names_id', 'idx_nodes_id'] )
            throw new \Exception(print_r($indexes, true));

        return true;
    }

}