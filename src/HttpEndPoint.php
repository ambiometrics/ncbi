<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 08-08-18
 * Time: 15:14
 */

namespace edwrodrig\ncbi;


use edwrodrig\ncbi\taxonomy\Service;

class HttpEndPoint
{

    private $database;


    private $request;

    public function __construct(array $request) {

    }

    public function getAction() : string {
        return $this->request['action'] ?? null;
    }

    public function getTaxIds() : array {
        return $this->request['taxids'] ?? [];
    }

    public function getService() {
        return new Service($this->database . '/db.sqlite3');
    }

    public function service(array $post)
    {
        $action = $this->getAction();

        if ( $action === 'get_scientific_name') {
            $this->actionGetScientificName();
        } else if ( $action == 'get_parent_id' ) {
            $this->actionGetParentIdNode();
        }
    }

    public function actionGetScientificName() : array {
        $service = $this->getService();


        return array_map(
            function($tax_id) use ($service) {
                return $service->getNameByIdNode($tax_id);
            },
            $this->getTaxIds()
        );
    }

    public function actionGetParentIdNode() : array {
        $service = $this->getService();

        return array_map(
            function($tax_id) use ($service) {
              return $service->getParentIdNodeByIdNode($tax_id);
            },
            $this->getTaxIds()
        );
    }
}