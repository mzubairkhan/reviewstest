<?php

namespace CAP\User;

use CAP\Core\Common;
use CAP\Core\DB;

Class Manager {

    private $db;
    private $table;

    function __construct()
    {
        $this->db = new DB('DB-BI');
        $this->table = array('users');
    }

    public function getUsers()
    {
        return $this->getActiveUsers();
    }

    public function getUserFromUserName($params)
    {
        return $this->db_getUserFromUserName($params);
    }

    public function get($params)
    {
        return $this->db_getActiveUser($params);
    }

    public function getUser($params)
    {
        $user_coupon_types = $this->db_getActiveUserCouponTypes($params);
        $user = $this->db_getActiveUser($params);
        $result['user'] = $user;
        $extra = array();
        foreach($user_coupon_types as $user_coupon_type)
        {
            if($user_coupon_type['coupon_type'] == 0){
                $extra['percent'] = array(
                    'percentValue' => $user_coupon_type['coupon_type_value'],
                    'isPercent' => $user_coupon_type['is_active']
                );
            } else {
                $extra['fix'] = array(
                    'fixValue' => $user_coupon_type['coupon_type_value'],
                    'isFix' => $user_coupon_type['is_active']
                );
            }
        }
        $result['extra'] = $extra;

        return $result;
    }

    public function isAdmin($params)
    {
        return $this->db_isAdmin($params);
    }

    public function insertUser($data)
    {
        $user = $this->db_insertUser($data);
        $extra = json_decode($_POST['extra'], true);
        $cTypes = Common::getCouponTypes();
        $params = array(
            'user_id' => $user['id'],
            'coupon_type' => $cTypes['percentage'],
            'coupon_type_value' => $extra['percentValue'],
            'is_active' => $extra['isPercent']
        );
        $this->db_insertUserExtra($params);
        $params = array(
            'user_id' => $user['id'],
            'coupon_type' => $cTypes['fix'],
            'coupon_type_value' => $extra['fixValue'],
            'is_active' => $extra['isFix']
        );
        $this->db_insertUserExtra($params);
        return true;
    }

    public function updateUser($data)
    {
        // first remove User Extra ( coupon types )

        $this->db_removeUserCouponTypes($data);

        $user_id = $data['user_id'];
        $extra = json_decode($_POST['extra'], true);
        $cTypes = Common::getCouponTypes();
        $params = array(
            'user_id' => $user_id,
            'coupon_type' => $cTypes['percentage'],
            'coupon_type_value' => $extra['percentValue'],
            'is_active' => $extra['isPercent']
        );
        $this->db_insertUserExtra($params);
        $params = array(
            'user_id' => $user_id,
            'coupon_type' => $cTypes['fix'],
            'coupon_type_value' => $extra['fixValue'],
            'is_active' => $extra['isFix']
        );
        $this->db_insertUserExtra($params);
        return true;
    }

    public function login($user)
    {
        return $this->db_login($user);
    }

    public function getTokenByUserId($userId)
    {
        $settings = Common::getSettings();
        $salt = $settings['salt']['key'];
        $data = $userId.$salt;
        $token = hash('sha256',$data);
        return $token;
    }

    public function addIP($params)
    {

        if(!$this->db_checkIP($params)) {
            $this->db_addIP($params);
            $this->addIPBash($params);
        } else {
            throw new \Exception('IP already exists.');

        }
    }

    public function addIPBash($params)
    {
        // add ip in iptables
        $settings = Common::getSettings();
        $bash = $settings['bash'];
        $ip_script = $bash['ip_manage'];
        $command = 'sudo '.$ip_script. ' open '.$params['ip'].' '.$params['notes'];
        $output = shell_exec($command);
        if($output == 'success') {
            return true;
        }
    }

    public function removeIPBash($params)
    {
        // add ip in iptables
        $settings = Common::getSettings();
        $bash = $settings['bash'];
        $ip_script = $bash['ip_manage'];
        $command = 'sudo '.$ip_script. ' close '.$params['ip'].' '.$params['notes'];
        $output = shell_exec($command);
        if($output == 'success') {
            return true;
        }
    }

    public function getIPs($params)
    {
        return $this->db_getIPs($params);
    }

    public function getIP($params)
    {
        return $this->db_getIP($params);
    }

    public function removeIP($params)
    {
        return $this->db_removeIP($params);
    }

    public function checkIP($params)
    {
        return $this->db_checkIP($params);
    }


    private function db_removeUserCouponTypes($params)
    {
        $input = array(
            'p_id' => $params['user_id']
        );
        $query = "DELETE from user_coupon_types where user_id = :p_id";
        $result = $this->db->query($query, $input);
        return $result;
    }

    private function db_checkIP($params)
    {
        $input = array(
            'p_user_id' => $params['user_id'],
            'p_ip' => $params['ip']
        );
        $query = "SELECT COUNT(*) as count
                    FROM bi_ip_users i
                    WHERE ip = :p_ip AND user_id = :p_user_id";
        $result = $this->db->row($query, $input);
        if($result['count'] > 0) {
            return true;
        }
        return false;
    }

    private function db_getIP($params)
    {
        $input = array(
            'p_id' => $params['id']
        );
        $query = "SELECT *
                    FROM bi_ip_users i
                    WHERE id = :p_id";
        $result = $this->db->row($query, $input);
        return $result;
    }

    private function db_getIPs()
    {
        $input = array();
        $query = "SELECT i.id as id, u.id as user_id,  u.username , i.ip, i.notes
                    FROM bi_ip_users i
                    LEFT JOIN users u ON u.id = i.user_id";
        $result = $this->db->query($query, $input);
        return $result;
    }

    private function db_addIP($params)
    {
        $input = array(
            'p_ip' => $params['ip'],
            'p_user_id' => $params['user_id'],
            'p_notes' => $params['notes']
        );
        $query = "INSERT INTO bi_ip_users (ip, user_id, notes, created) VALUES(:p_ip, :p_user_id, :p_notes, NOW())";

        if(isset($params['id'])) {
            $input['p_id'] = $params['id'];

            $query = "UPDATE bi_ip_users SET ip = :p_ip, user_id = :p_user_id, notes = :p_notes WHERE id = :p_id";
        }

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

    private function db_removeIP($params)
    {
        $input = array(
            'p_id' => $params['id']
        );
        $query = "DELETE from bi_ip_users where id = :p_id";
        $result = $this->db->query($query, $input);
        return $result;
    }

    private function db_isAdmin($params)
    {
        $input = array(
            'p_user_id' => $params['user_id']
        );
        $query = "SELECT COUNT(*) as count FROM ".$this->table[0]." WHERE id = :p_user_id AND is_admin = 1 AND is_active = 1";
        $result = $this->db->row($query, $input);
        if($result['count'] > 0) {
            return true;
        }
        return false;
    }

    private function db_getUserFromUserName($params)
    {
        $input = array(
            'p_username' => $params['username']
        );
        $query = "SELECT id, username, first_name , last_name , email from ".$this->table[0]." WHERE username = :p_username AND is_active = 1";
        $result = $this->db->row($query, $input);
        return $result;
    }

    private function db_getActiveUser($params)
    {
        $input = array(
            'p_user_id' => $params['user_id']
        );
        $query = "SELECT id, username, first_name , last_name , email from ".$this->table[0]." WHERE id = :p_user_id AND is_active = 1";
        $result = $this->db->row($query, $input);
        return $result;
    }

    private function db_getActiveUserCouponTypes($params)
    {
        $input = array(
            'p_user_id' => $params['user_id']
        );
        $query = "SELECT coupon_type, coupon_type_value, is_active from user_coupon_types WHERE user_id = :p_user_id";
        $result = $this->db->query($query, $input);
        return $result;
    }

    private function getActiveUsers()
    {
        $input = array();
        $query = "SELECT id, username, first_name , last_name , email from ".$this->table[0]." WHERE is_active = 1";
        $result = $this->db->query($query, $input);
        return $result;
    }

    private function db_login($user)
    {
        $query = "SELECT id, count(*) as count , is_admin FROM ".$this->table[0]." WHERE username = :username AND password = :password AND is_active = 1";
        $result = $this->db->row($query, $user, \PDO::FETCH_ASSOC);
        if($result['count'] > 0) {
            return $result;
        }
        return false;
    }

    private function db_insertUserExtra($params)
    {

        $input = array(
            'p_user_id' => $params['user_id'],
            'p_coupon_type' => $params['coupon_type'],
            'p_coupon_type_value' => $params['coupon_type_value'],
            'p_is_active' => $params['is_active']
        );

        $query = "INSERT INTO user_coupon_types (user_id, coupon_type, coupon_type_value, is_active) VALUES(:p_user_id, :p_coupon_type, :p_coupon_type_value, :p_is_active)";
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

    private function db_insertUser($params)
    {
        $input = array(
            'p_username' => $params['username'],
            'p_first_name' => $params['first_name'],
            'p_last_name' => $params['last_name'],
            'p_password' => md5($params['password']),
            'p_email' => $params['email'],
        );

        $query = "INSERT INTO ".$this->table[0]."(username, first_name, last_name, email, password, is_active, created, modified) VALUES(:p_username, :p_first_name, :p_last_name, :p_email, :p_password, 1, NOW() , NOW())";
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

}