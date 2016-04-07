<?php

namespace CAP\Media;

use CAP\Core\Common;
use CAP\Core\DB;

Class Manager {

    private $db;
    private $table;

    function __construct()
    {
        $this->db = new DB('DB-BI');
        $this->table = array('media');
    }

    public function create($params = ''){
        return $this->db_create($params);
    }


    public function getAll($items_count) {
        return $this->db_getAll($items_count);
    }

    private function db_create($params) {
        $input = array(
            'p_file_name' => $params
        );

        $query = "INSERT INTO ".$this->table[0]." (file_name) VALUES(:p_file_name)";

        $result = $this->db->query($query, $input);
        $resultId = 0;
        if($result) {
            $resultId = $this->db->lastInsertId();
        }
        $response = array(
            'id' => $resultId
        );
        return $response;
    }

    private function db_getAll($items_count) {
        $input = array();
        $query = "SELECT * from ".$this->table[0];
        $result = $this->db->query($query, $input);
        return $result;
    }

}