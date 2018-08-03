<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 01-08-18
 * Time: 16:11
 */

namespace edwrodrig\ncbi\taxonomy;

use PDO;

class Service
{
    /**
     * @var PDO
     */
    private $pdo;

    public function __construct(string $filename) {
        $this->pdo = new PDO("sqlite:" . $filename);
    }

    /**
     * @param int $id_node
     * @return null|int
     */
    public function getParentIdNodeByIdNode(int $id_node) : ?int {
        $stmt = $this->pdo->prepare('SELECT parent_id FROM nodes WHERE id = ?');
        $stmt->bindValue(1, $id_node, PDO::PARAM_INT);
        $stmt->execute();

        $stmt->bindColumn(1,$parent_id, PDO::PARAM_INT);

        if ( $stmt->fetch(PDO::FETCH_BOUND) )
            return $parent_id;
        else
            return null;
    }

    /**
     * @param int $id_node
     * @return null|string
     */
    public function getNameByIdNode(int $id_node) : ?string {
        $stmt = $this->pdo->prepare('SELECT name FROM names WHERE id = ?');
        $stmt->bindValue(1, $id_node, PDO::PARAM_INT);
        $stmt->execute();

        $stmt->bindColumn(1,$name, PDO::PARAM_STR);

        if ( $stmt->fetch(PDO::FETCH_BOUND))
            return $name;
        else
            return null;
    }
}