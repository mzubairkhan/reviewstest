<?php

namespace CAP\Page;

use CAP\Core\Common;
use CAP\Core\DB;

Class Manager {

    private $db;
    private $table;

    function __construct()
    {
        $this->db = new DB('DB-BI');
        $this->table = array('pages');
    }

    public function getPages($params = ''){
       return $this->db_getPages($params);
    }

    public function insertPage($params){
       return $this->db_insertPage($params);
    }

    public function updatePage($params){
       return $this->db_updatePage($params);
    }

    public function getPage($params){
       return $this->db_getPage($params);
    }

    public function removePage($params){
        return $this->db_removePage($params);
    }



    private function db_getPages($params) {
        //$input = array('p_id' => $params['id']);

        $website_id = $_REQUEST['website_id'];
        if(!empty($website_id)){
            $query = "SELECT p.id, p.title, p.status, w.title as website, p.created_on FROM ".$this->table[0]." as p LEFT JOIN websites as w on p.website_id=w.id WHERE p.website_id=".$website_id."  ORDER BY w.title ASC; ";
        }else{
            $query = "SELECT p.id, p.title, p.status, w.title as website, p.created_on FROM ".$this->table[0]." as p LEFT JOIN websites as w on p.website_id=w.id ORDER BY w.title ASC; ";
        }
        $result = $this->db->query($query);

        return $result;
    }


    private function db_insertPage($params) {


        $input = array(
            'p_title' => $params['title'],
            'p_status' => $params['status'],
            'p_website_id' => $params['website_id'],
            'p_meta_details' => json_encode($params['meta']),
            'p_content' => $params['content'],
        );


        $query = "INSERT INTO ".$this->table[0]."(title, status, website_id, meta_details, content, created_on) VALUES(:p_title, :p_status, :p_website_id, :p_meta_details, :p_content, NOW())";
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

    private function db_updatePage($params) {


        $input = array(
            'p_page_id' => $params['page_id'],
            'p_title' => $params['title'],
            'p_status' => $params['status'],
            'p_website_id' => $params['website_id'],
            'p_meta_details' => json_encode($params['meta']),
            'p_content' => $params['content'],
        );

        $query = "UPDATE ".$this->table[0]." SET title = :p_title, status = :p_status, website_id = :p_website_id, meta_details = :p_meta_details, content = :p_content WHERE id = :p_page_id ";
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

    private function db_getPage($params){
        $input = array(
            'p_id' => $params
        );

        $query = "SELECT * FROM ".$this->table[0]." WHERE id = :p_id";
        $result = $this->db->query($query, $input);

        return $result;
    }

    private function db_removePage($params) {
        $input = array(
            'p_page_id' => $params
        );

        $query = "DELETE FROM ".$this->table[0]." WHERE id = :p_page_id";
        $result = $this->db->query($query, $input);
        return $result;
    }

}