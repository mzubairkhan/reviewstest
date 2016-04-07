<?php

namespace CAP\CustomField;

use CAP\Core\Common;
use CAP\Core\DB;

Class Manager {

    private $db;
    private $table;

    function __construct()
    {
        $this->db = new DB('DB-BI');
        $this->table = array('custom_fields');
    }

    public function createGroup($params){
        return $this->db_createGroup($params);
    }

    public function createField($params) {
        return $this->db_createField($params);
    }

    public function listAll(){
        return $this->db_listAll();
    }

    public function updateGroup($params){
        return $this->db_updateGroup($params);
    }

    public function updateField($params) {
        return $this->db_updateField($params);
    }

    public function removeField($params){
        return $this->db_removeField($params);
    }
    public function removeFieldGroup($params){
        return $this->db_removeFieldGroup($params);
    }

    public function getFieldGroup($id) {
        return $this->db_getFieldGroup($id);
    }

    public function getField($id) {
        return $this->db_getField($id);
    }

    public function getGroupFields($group_id, $orderby) {
        return $this->db_getGroupFields($group_id, $orderby);
    }

    public function getFieldsByModule($params) {
        return $this->db_getFieldsByModule($params);
    }

    public function updateFieldByModule($params) {
        return $this->db_updateFieldByModule($params);
    }

    public function getFieldValue($param) {
        return $this->db_getFieldValue($param);
    }



    private function db_listAll() {

        $input = array();
        $query = "SELECT `id`, `title`, `module_type`, `status` from custom_field_groups";
        $result = $this->db->query($query, $input);
        return $result;
    }

    private function db_createGroup($params) {

        $input = array(
            'p_title' => $params['title'],
            'p_module_type' => $params['module_type'],
            'p_status' => $params['status']
        );


        $query = "INSERT INTO custom_field_groups ( `title`, module_type, status, created_on) VALUES(:p_title, :p_module_type, :p_status, NOW())";
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

    private function db_createField($params) {

        $input = array(
            'p_title' => $params['title'],
            'p_key' => $params['key'],
            'p_module_type' => $params['module_type'],
            'p_input_type' => $params['input_type'],
            'p_default_value' => $params['default_value'],
            'p_group_id' => $params['group_id'],
        );


        $query = "INSERT INTO ".$this->table[0]." ( title, `key`, module_type, input_type, default_value, group_id, created_on) VALUES(:p_title, :p_key, :p_module_type, :p_input_type,:p_default_value, :p_group_id, NOW())";
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

    private function db_updateGroup($params) {
       // print_r($params);exit;
        $input = array(
            'p_id' => $params['id'],
            'p_title' => $params['title'],
            'p_module_type' => $params['module_type'],
            'p_status' => $params['status'],

        );


        $query = "UPDATE custom_field_groups SET `title` = :p_title, module_type = :p_module_type, status = :p_status WHERE id = :p_id";
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

    private function db_updateField($params) {
        $input = array(
            'p_id' => $params['field_id'],
            'p_title' => $params['title'],
            'p_key' => $params['key'],
            'p_module_type' => $params['module_type'],
            'p_input_type' => $params['input_type'],
            'p_default_value' => $params['default_value']
        );


        $query = "UPDATE ".$this->table[0]." SET title = :p_title, `key` = :p_key, module_type = :p_module_type, input_type = :p_input_type, default_value = :p_default_value WHERE id = :p_id";
        $result = $this->db->query($query, $input);
        $resultId = 0;
       // print_r($result);exit;

        if($result) {
            $resultId = $this->db->lastInsertId();
        }


        $response = array(
            'id' => $resultId
        );
        return $response;
    }

    private function db_removeField($params) {

        $input = array(
            'p_id' => $params
        );

        $query = "DELETE FROM " .$this->table[0]." WHERE id = :p_id ";
        $result = $this->db->query($query, $input);

        $query = "DELETE FROM  custom_fields_value WHERE custom_field_id = :p_id ";
        $result = $this->db->query($query, $input);

        return $result;
    }

    private function db_removeFieldGroup($params) {

        $input = array(
            'p_id' => $params
        );

        $query = "DELETE FROM custom_field_groups WHERE id = :p_id ";
        $result = $this->db->query($query, $input);

        $query = "SELECT `id` FROM `custom_fields` WHERE group_id = :p_id";
        $result = $this->db->query($query, $input);

        foreach($result as $k=>$v){
            $query = "DELETE FROM custom_fields_value WHERE custom_field_id = ".$v['id'];
            $result = $this->db->query($query);
        }

        $query = "DELETE FROM custom_fields WHERE group_id = :p_id ";
        $result = $this->db->query($query, $input);


        return $result;
    }

    private function db_getFieldGroup($id) {
        $input = array(
            'p_id' => $id
        );

        $query = "SELECT * FROM custom_field_groups  WHERE id = :p_id";
        $result = $this->db->query($query, $input);

        return $result;
    }

    private function db_getField($id) {
        $input = array(
            'p_id' => $id
        );

        $query = "SELECT cf.key, cf.relation_id, cf.module_type,cfv.value FROM " .$this->table[0]. " as cf INNER JOIN custom_fields_value cfv ON cf.id = cfv.custom_field_id WHERE cf.id = :p_id";
        $result = $this->db->query($query, $input);

        return $result;
    }

    private function db_getGroupFields($group_id, $orderby = "input_type") {

        $input = array(
            'p_id' => $group_id,
        );


        $query = "SELECT * FROM " .$this->table[0]. "  WHERE group_id = :p_id ORDER BY ".$orderby;
        $result = $this->db->query($query, $input);

        return $result;
    }

    private function db_getFieldsByModule($params) {
        $input = array(
            'p_module_type' => $params["module_type"],
        );

        $query = "SELECT * FROM custom_field_groups WHERE module_type = :p_module_type AND status = 1";
        $result = $this->db->query($query, $input);

        if($result) {
            $i = 0;
            foreach($result as $key => $val ) {
                $result[$i]['group_fields'] = $this->db_getGroupFields($val["id"]);
                $x=0;
                foreach($result[$i]['group_fields'] as $k=>$v) {
                    $field_value = $this->db_getFieldValueByRel($v['id'], $params['relation_id']);
                    $result[$i]['group_fields'][$x]['value'] = $field_value[0]['value'];
                   $x++;
                }
                $i++;
            }
        }

        return $result;

    }

    private function db_updateFieldByModule($params) {

        $data = $params;
        foreach($data['custom_fields'] as $key=>$val) {


                if(is_array($val['value'])) {
                    $val['value'] = implode(',', $val['value']);
                }

                $remove = array(
                    'p_custom_field_id' => $val['id'],
                    'p_rel_id' => $data['relation_id'],
                );

                $input = array(
                    'p_custom_field_id' => $val['id'],
                    'p_value' => $val['value'],
                    'p_rel_id' => $data['relation_id'],
                );

             $query = "DELETE FROM custom_fields_value WHERE rel_id = :p_rel_id AND custom_field_id = :p_custom_field_id";
             $result = $this->db->query($query, $remove);

            if(!empty($val['value'])) {
                $query = "INSERT INTO custom_fields_value (custom_field_id, `value`, rel_id) VALUES(:p_custom_field_id, :p_value, :p_rel_id)";
                $result = $this->db->query($query, $input);
            }

            $resultId = 0;
            if($result) {
                $resultId = $this->db->lastInsertId();
            }


                $response = array();
                $response[$key]['id'] = $resultId;


            }


        return $response;
    }

    private function db_getFieldValue($params) {
        $input = array(
            'p_key' => $params['key'],
            'p_rel_id' => $params['rel_id'],
        );
        $query = "SELECT cf.id, cf.key, cfv.value  FROM custom_fields as cf LEFT JOIN custom_fields_value as cfv on cf.id=cfv.custom_field_id WHERE cf.key = :p_key AND cfv.rel_id = :p_rel_id";
        $result = $this->db->query($query, $input);
       // print_r($result);exit;
        return $result;
    }


    private function db_getFieldValueByRel($custom_fields_id, $rel_id) {


        $query = "SELECT `value` FROM custom_fields_value WHERE custom_field_id = ".$custom_fields_id." AND rel_id = ".$rel_id;
        $result = $this->db->query($query);

        return $result;
    }
}