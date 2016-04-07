<?php

namespace CAP\GitRepo;

use CAP\Core\Common;
use CAP\Core\DB;

Class Manager {

    private $db;
    private $table;

    function __construct()
    {
        $this->db = new DB('DB-BI');
        $this->table = array('websites');
    }

    public function listAll() {

        return $this->db_listAll();
    }


    private function db_listAll(){

        $input = array();
        $query = "SELECT * from websites";
        $result = $this->db->query($query, $input);
        return $result;
    }


}