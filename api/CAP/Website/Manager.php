<?php

namespace CAP\Website;

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

    public function createWebsite($params) {
        return $this->db_createWebsite($params);
    }

    public function getWebsite($website_id) {
        return $this->db_getWebsite($website_id);
    }

    public function updateWebsite($params) {
        return $this->db_updateWebsite($params);
    }

    private function db_listAll(){

        $input = array();
        $query = "SELECT `id`, `title`, `domain` from websites";
        $result = $this->db->query($query, $input);
        return $result;
    }

    private function db_createWebsite($params) {

        $input = array(
            'p_title' => $params['title'],
            'p_domain' => $params['domain'],
            'p_server_ip' => $params['server_ip'],
            'p_git_repo' => $params['git_repo'],
            'p_website_type' => $params['website_type'],
            'p_meta_details' => json_encode($params['meta']),
            'p_setting' => json_encode($params['setting']),
            'p_theme' => json_encode($params['theme']),

        );


        $query = "INSERT INTO ".$this->table[0]."( title, `domain`, server_ip, git_repo, website_type, meta_details, setting, theme, created_on) VALUES(:p_title, :p_domain, :p_server_ip, :p_git_repo, :p_website_type, :p_meta_details, :p_setting, :p_theme, NOW())";
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

    private function db_updateWebsite($params) {

        $input = array(
            'p_website_id' => $params['website_id'],
            'p_title' => $params['title'],
            'p_domain' => $params['domain'],
            'p_server_ip' => $params['server_ip'],
            'p_git_repo' => $params['git_repo'],
            'p_website_type' => $params['website_type'],
            'p_meta_details' => json_encode($params['meta']),
            'p_setting' => json_encode($params['setting']),
            'p_theme' => json_encode($params['theme']),
        );

        $query = "UPDATE ".$this->table[0]." SET title = :p_title, domain = :p_domain, server_ip = :p_server_ip, git_repo = :p_git_repo, website_type = :p_website_type, meta_details = :p_meta_details, setting = :p_setting, theme = :p_theme WHERE id = :p_website_id ";
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

    private function db_getWebsite($website_id) {
        $input = array(
            'p_id' => $website_id
        );

        $query = "SELECT * FROM ". $this->table[0] ." WHERE id = :p_id ";
        $result = $this->db->query($query, $input);
        return $result;


    }

}