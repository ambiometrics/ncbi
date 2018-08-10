<?php
declare(strict_types=1);

namespace edwrodrig\ncbi\taxonomy;

use SQLite3;

/**
 * Class Dao(Data Access Object)
 *
 * This class is the basic connection of the taxonomic data builder with the {@see Builder builder}
 * @package edwrodrig\ncbi\taxonomy
 */
class Dao
{
    /**
     * The SQLite object that connects to the sqlite database
     * @var SQLite3
     */
    private $db;

    /**
     * Dao constructor.
     *
     * You must provide a filename with a valid sqlite3 database that matches the format of {@see Builder generated databases}
     * @param string $filename
     */
    public function __construct(string $filename) {
        $this->db = new SQLite3($filename,  SQLITE3_OPEN_READWRITE);
    }

    /**
     * Get the parent id node by id node
     *
     * Get the parent tax id from the current tax id
     * @param int $id_node
     * @return null|int
     */
    public function getParentIdNodeByIdNode(int $id_node) : ?int {
        $stmt = $this->db->prepare('SELECT parent_id FROM nodes WHERE id = ?');
        $stmt->bindValue(1, $id_node,  SQLITE3_INTEGER);
        $result = $stmt->execute();

        if ( $row = $result->fetchArray(SQLITE3_NUM) )
            return $row[0];
        else
            return null;
    }

    /**
     * Get the name (scientific name) of a tax node by tax id
     *
     * @param int $id_node
     * @return null|string May always return a name, returns null otherwise
     */
    public function getScientificNameByIdNode(int $id_node) : ?string {
        $stmt = $this->db->prepare('SELECT name FROM names WHERE id = ?');
        $stmt->bindValue(1, $id_node,  SQLITE3_INTEGER);
        $result = $stmt->execute();

        if ( $row = $result->fetchArray(SQLITE3_NUM))
            return $row[0];
        else
            return null;
    }

    /**
     * Validate the database.
     *
     * The validation just check if the database has the correct tables and indexes.
     * If the database is not valid throws
     * @return bool
     * @throws exception\DaoValidationException
     */
    public function validate() : bool {
        $this->checkTables();
        $this->checkIndexes();
        return true;
    }

    /**
     * Check the database tables
     *
     * Throws if the database does not have the tables
     * @return bool
     * @throws exception\DaoValidationException
     */
    public function checkTables() : bool {
        $result = $this->db->query('SELECT name FROM sqlite_master WHERE type = "table"');

        $tables = [];
        while ( $row = $result->fetchArray(SQLITE3_NUM) ) {
            $tables[] = $row[0];
        }

        if ( $tables != ['names', 'nodes'] )
            throw new exception\DaoValidationException(print_r($tables, true));

        return true;
    }

    /**
     * Check the indexes
     *
     * Throws if the database does not have the indexes
     * @return bool
     * @throws exception\DaoValidationException
     */
    public function checkIndexes() : bool {
        $result = $this->db->query('SELECT name FROM sqlite_master WHERE type = "index"');

        $indexes = [];
        while ( $row = $result->fetchArray(SQLITE3_NUM) ) {
            $indexes[] = $row[0];
        }

        if ( $indexes != ['idx_names_id', 'idx_nodes_id'] )
            throw new exception\DaoValidationException(print_r($indexes, true));

        return true;
    }
}