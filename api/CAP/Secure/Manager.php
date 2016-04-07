<?php

namespace CAP\Secure;

use CAP\User\Manager as UserManager;
use CAP\Acl\Manager as AclManager;

use CAP\Core\Common;
use CAP\Core\DB;

Class Manager {

    private $db;
    private $table;
    private $userObj;
    private $aclObj;

    function __construct()
    {
        $this->db = new DB('DB-BI');
        $this->table = array('users');
        $this->userObj = new UserManager();
        $this->aclObj = new AclManager();

    }

    public function createReseller($params)
    {
        $params['email'] = $params['username'].'@rpurevpn.com';
        $params['first_name'] = $params['username'];
        $params['last_name'] = '';
        $params['password'] = md5($params['username']);

        // insert User
        $user = $this->userObj->insertUser($params);
        $userId = $user['id'];

        // get reseller Role -> ID
        $title = 'Reseller';
        $params_gs = array('title' => 'Reseller');
        $role_reseller = $this->aclObj->getRoleFromTitle($params_gs);
        $roleIds = array('id'=> $role_reseller['id']);

        //assign user -> reseller Role
        $params_ur = array(
            'user_id' => $userId,
            'role_ids' => $roleIds
        );
        $this->aclObj->associateRolesToUser($params_ur);

        $token = $this->userObj->getTokenByUserId($userId);

        $response = array(
            'user_id' => $userId,
            'token' => $token
        );

        return $response;
    }

    public function resellerExists($params)
    {
        $usrObj = new UserManager();
        $user = $usrObj->getUserFromUserName($params);
        if(isset($user['id'])) {
            return true;
        }
    }

    public function getReseller($params)
    {
        $usrObj = new UserManager();
        $user = $usrObj->getUserFromUserName($params);
        $userId = $user['id'];

        $token = $this->userObj->getTokenByUserId($userId);

        $response = array(
            'user_id' => $userId,
            'token' => $token
        );
        return $response;
    }

}