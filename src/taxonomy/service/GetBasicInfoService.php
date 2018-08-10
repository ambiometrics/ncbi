<?php
declare(strict_types=1);

namespace edwrodrig\ncbi\taxonomy\service;

use edwrodrig\ncbi\taxonomy\Dao;

class GetBasicInfoService
{
    /**
     * The folder where the database files are contained
     * @var string folder
     */
    private $data_folder;

    /**
     * @var array An array with the data of the
     */
    private $request;

    /**
     * @var null|Dao
     */
    private $dao = null;

    /**
     * GetBasicInfoService constructor.
     * @param array $request
     */
    public function __construct(array $request) {
        $this->request = $request;
    }

    /**
     * @param string $folder
     * @return GetBasicInfoService
     */
    public function setDataFolder(string $folder) : GetBasicInfoService {
        $this->data_folder = $folder;
        return $this;
    }

    /**
     * Get the request tax ids
     * @return array
     */
    private function getTaxIds() : array {
        return $this->request['taxids'] ?? [];
    }

    /**
     * Get the dao
     * @return Dao
     */
    private function getDao() : Dao {
        if ( is_null($this->dao) )
            $this->dao =  new Dao($this->data_folder . '/db.sqlite3');

        return $this->dao;
    }

    /**
     * Get the basic info of a tax id
     *
     * The basic info is the name and the parent_id
     * @param int $tax_id
     * @return array
     */
    public function getBasicInfo(int $tax_id) : array {
        $dao = $this->getDao();

        return [
            'name' => $dao->getScientificNameByIdNode($tax_id),
            'parent_id' => $dao->getParentIdNodeByIdNode($tax_id)
        ];
    }

    /**
     * Process all the tax_ids
     * @return array
     */
    public function process() : array
    {
        $response = [];
        foreach ( $this->getTaxIds() as $tax_id ) {
            if ( isset($response[$tax_id])) continue;

            $response[$tax_id] = $this->getBasicInfo($tax_id);
        }

        return $response;
    }
}