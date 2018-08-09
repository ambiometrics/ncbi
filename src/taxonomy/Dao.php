<?php
declare(strict_types=1);

namespace edwrodrig\ncbi\taxonomy;

use SQLite3;

class Dao
{
    /**
     * @var SQLite3
     */
    private $db;

    public function __construct(string $filename) {
        $this->db = new SQLite3($filename,  SQLITE3_OPEN_READWRITE);
    }

    /**
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
     * @param int $id_node
     * @return null|string
     */
    public function getNameByIdNode(int $id_node) : ?string {
        $stmt = $this->db->prepare('SELECT name FROM names WHERE id = ?');
        $stmt->bindValue(1, $id_node,  SQLITE3_INTEGER);
        $result = $stmt->execute();

        if ( $row = $result->fetchArray(SQLITE3_NUM))
            return $row[0];
        else
            return null;
    }
}