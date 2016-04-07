<?php

namespace CAP\Provider;

use CAP\Core\Common;
use CAP\Core\DB;

Class Manager {

    private $db;
    private $table;

    function __construct()
    {
        $this->db = new DB('DB-BI');
        $this->table = array('providers');
    }

    public function getAll($params = ''){
       return $this->db_getAll($params);
    }

    public function create($params){
       return $this->db_create($params);
    }

    public function update($params){
       return $this->db_update($params);
    }

    public function get($params){
       return $this->db_get($params);
    }

    public function remove($params){
        return $this->db_remove($params);
    }



    private function db_getAll($params) {


        $input = array();
        $query = "SELECT id, title, price, discount from ".$this->table[0];
        $result = $this->db->query($query, $input);
        return $result;
    }


    private function db_create($params) {


        $input = array(
            'p_title' => $params['title'],
            'p_description' => $params['description'],
            'p_logo' => $params['logo'],
            'p_status' => $params['status'],
            'p_price' => $params['price'],
            'p_discount' => $params['discount'],
            'p_visit_link' => $params['visit_link'],
        );


        $query = "INSERT INTO ".$this->table[0]." (title, description, logo, status, price, discount, visit_link, created_on) VALUES(:p_title, :p_description, :p_logo, :p_status, :p_price, :p_discount, :p_visit_link , NOW())";
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

    private function db_update($params) {


        $input = array(
            'p_provider_id' => $params['provider'],
            'p_title' => $params['title'],
            'p_description' => $params['description'],
            'p_logo' => $params['logo'],
            'p_status' => $params['status'],
            'p_price' => $params['price'],
            'p_discount' => $params['discount'],
            'p_visit_link' => $params['visit_link'],
        );

       // print_r($input);exit;

        $query = "UPDATE ".$this->table[0]." SET title = :p_title, description = :p_description, logo = :p_logo, price = :p_price, discount = :p_discount, status = :p_status, visit_link = :p_visit_link WHERE id = :p_provider_id ";
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

    private function db_get($params){
        $input = array(
            'p_id' => $params
        );

        $query = "SELECT * FROM ".$this->table[0]." WHERE id = :p_id";
        $result = $this->db->query($query, $input);

        return $result;
    }

    private function db_remove($params) {
        $input = array(
            'p_provider_id' => $params
        );

        $query = "DELETE v.* FROM `custom_fields` AS `f` INNER JOIN `custom_fields_value` `v` ON v.custom_field_id = f.id WHERE f.module_type = 'provider' AND v.rel_id = :p_provider_id";
        $result = $this->db->query($query, $input);

        $query = "DELETE FROM ".$this->table[0]." WHERE id = :p_provider_id";
        $result = $this->db->query($query, $input);
        return $result;
    }

}