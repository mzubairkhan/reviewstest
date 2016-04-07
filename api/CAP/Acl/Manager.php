<?php

namespace CAP\Acl;

use CAP\Core\Common;
use CAP\Core\DB;

Class Manager {

    private $db;
    private $table;

    function __construct()
    {
        $this->db = new DB('DB-BI');
        $this->table = 'users';
    }

    public function removeUser($params)
    {
        // remove from user_coupon_types first
        $this->db_removeUserCouponTypes($params);
        return $this->db_removeUser($params);
    }

    public function getUser($params) {
        return $this->db_getUser($params);
    }

    public function getRole($params) {
        return $this->db_getRole($params);
    }

    public function getRoleFromTitle($params) {
        return $this->db_getRoleFromTitle($params);
    }

    public function getPermission($params) {
        return $this->db_getPermission($params);
    }

    public function getModule($params) {
        return $this->db_getModule($params);
    }

    public function createRole($params)
    {
        return $this->db_createRole($params);
    }

    public function removeRole($params)
    {
        return $this->db_removeRole($params);
    }

    public function createPermission($params)
    {
        return $this->db_createPermission($params);
    }

    public function removePermission($params)
    {
        return $this->db_removePermission($params);
    }

    public function createModule($params)
    {
        return $this->db_createModule($params);
    }

    public function removeModule($params)
    {
        return $this->db_removeModule($params);
    }

    public function associatePermissionsToRole($params)
    {
        //1. remove Existing Permissions From Role
        $this->db_removeAllPermissionsFromRole($params);

        //2. Add Current Permission To Role
        $permissions_arr = $params['permission_ids'];
        foreach($permissions_arr as $perm)
        {
            $data = array(
                'role_id' => $params['role_id'],
                'perm_id' => $perm
            );
            $this->db_addPermissionToRole($data);
        }

        return true;
    }

    public function associateRolesToUser($params)
    {
        //1. remove Existing Roles From USer
        $this->db_removeAllRolesFromUser($params);

        //2. Add Current Roles From User
        $roles_arr = $params['role_ids'];
        foreach($roles_arr as $role)
        {
            $data = array(
                'user_id' => $params['user_id'],
                'role_id' => $role
            );
            $this->db_addRoleToUser($data);
        }

        return true;
    }

    public function getUserRoles($params)
    {
        return $this->db_getUserRoles($params);
    }

    public function getPermissionsRole($params)
    {
        return $this->db_getPermissionsRole($params);
    }

    public function getRolePermissions($params)
    {
        return $this->db_getRolePermissions($params);
    }

    public function getModulePermissions($params)
    {
        return $this->db_getModulePermissions($params);
    }

    public function getAllModulePermissions()
    {
        $perms = $this->db_getAllModulePermissions();
        $modules = array();
        foreach($perms as $perm)
        {
            if(!isset($modules[$perm['mId']])) {
                $modules[$perm['mId']] = array();
            }

            $modules[$perm['mId']][] = $perm;
        }
        return $modules;
    }

    public function getRoles()
    {
        return $this->db_getRoles();
    }

    public function getPermissions()
    {
        return $this->db_getPermissions();
    }

    public function getModules()
    {
        return $this->db_getModules();
    }

    public function getUserPermissions($params)
    {
        return $this->db_getUserPermissions($params);
    }










    private function db_getModule($params)
    {
        $input = array(
            'p_id' => $params['module_id']
        );
        $query = "SELECT * FROM modules WHERE id = :p_id";
        $result = $this->db->row($query, $input);
        return $result;
    }

    private function db_getPermission($params)
    {
        $input = array(
            'p_id' => $params['permission_id']
        );
        $query = "SELECT * FROM permissions WHERE id = :p_id";
        $result = $this->db->row($query, $input);
        return $result;
    }

    private function db_getRole($params)
    {
        $input = array(
            'p_id' => $params['role_id']
        );
        $query = "SELECT * FROM roles WHERE id = :p_id";
        $result = $this->db->row($query, $input);
        return $result;
    }

    private function db_getRoleFromTitle($params)
    {
        $input = array(
            'p_title' => $params['title']
        );
        $query = "SELECT * FROM roles WHERE title = :p_title";
        $result = $this->db->row($query, $input);
        return $result;
    }

    private function db_getUser($params)
    {
        $input = array(
            'p_id' => $params['user_id']
        );
        $query = "SELECT * FROM users WHERE id = :p_id";
        $result = $this->db->row($query, $input);
        return $result;
    }

    private function db_getModules()
    {
        $input = array();
        $query = "SELECT * FROM modules";
        $result = $this->db->query($query, $input);
        return $result;
    }

    private function db_getPermissions()
    {
        $input = array();
        $query = "SELECT P.id as id, P.title as title , P.is_backend as Backend , M.title as Module
                    FROM permissions P, modules M
                    WHERE M.id = P.module_id";
        $result = $this->db->query($query, $input);
        return $result;
    }

    private function db_getRoles()
    {
        $input = array();
        $query = "SELECT * FROM roles";
        $result = $this->db->query($query, $input);
        return $result;
    }

    private function db_getAllModulePermissions()
    {
        $input = array();
        $query = "SELECT P.id AS id, P.title title, P.is_backend, P.module_id as mId, M.title as mTitle
                    FROM modules M , permissions P
                    WHERE M.id = P.module_id";

        $result = $this->db->query($query, $input);
        return $result;
    }

    private function db_getModulePermissions($params)
    {
        $input = array(
            'p_module_id' => $params['module_id']
        );
        $query = "SELECT P.id , P.title , P.is_backend
                    FROM permissions P
                    WHERE P.module_id = :p_module_id";

        $result = $this->db->query($query, $input);
        return $result;
    }

    private function db_getRolePermissions($params)
    {
        $input = array(
            'p_role_id' => $params['role_id']
        );
        $query = "SELECT P.id , P.title , P.is_backend
                    FROM roles R
                    INNER JOIN role_perm RP
                       ON RP.role_id = R.id
                    INNER JOIN permissions P
                       ON RP.perm_id = P.id
                    WHERE R.id = :p_role_id";

        $result = $this->db->query($query, $input);
        return $result;
    }

    private function db_getPermissionsRole($params)
    {
        $input = array(
            'p_permission_id' => $params['permission_id']
        );
        $query = "SELECT R.id , R.title
                    FROM roles R
                    INNER JOIN role_perm RP
                       ON RP.role_id = R.id
                    INNER JOIN permissions P
                       ON RP.perm_id = P.id
                    WHERE P.id = :p_permission_id";

        $result = $this->db->query($query, $input);
        return $result;
    }

    private function db_getUserRoles($params)
    {
        $input = array(
            'p_user_id' => $params['user_id']
        );
        $query = "SELECT R.id , R.title
                    FROM users U
                    INNER JOIN user_role UR
                       ON UR.user_id = U.id
                    INNER JOIN roles R
                       ON UR.role_id = R.id
                    WHERE U.id = :p_user_id";

        $result = $this->db->query($query, $input);
        return $result;
    }

    private function db_getUserPermissions($params)
    {
        $input = array(
            'p_user_id' => $params['user_id']
        );
        if(isset($params['is_backend'])) {
            $input['p_is_backend'] = $params['is_backend'];
        }
        $query = "SELECT P.id, P.title, P.is_backend, P.module_id
                    FROM users U
                    INNER JOIN user_role UR
                       ON UR.user_id = U.id
                    INNER JOIN roles R
                       ON UR.role_id = R.id
                    INNER JOIN role_perm RP
                       ON RP.role_id = R.id
                    INNER JOIN permissions P
                       ON RP.perm_id = P.id
                    WHERE U.id = :p_user_id ";
        if(isset($params['is_backend'])) {
            $query .= " AND P.is_backend = :p_is_backend";
        }
        $result = $this->db->query($query, $input);
        return $result;
    }

    private function db_addRoleToUser($params)
    {
        $input = array(
            'p_user_id' => $params['user_id'],
            'p_role_id' => $params['role_id']
        );
        $query = "INSERT INTO user_role (user_id, role_id) VALUES(:p_user_id, :p_role_id)";
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

    private function db_removeAllRolesFromUser($params)
    {
        $input = array(
            'p_id' => $params['user_id']
        );
        $query = "DELETE from user_role where user_id = :p_id";
        $result = $this->db->query($query, $input);
        return $result;
    }

    private function db_addPermissionToRole($params)
    {
        $input = array(
            'p_role_id' => $params['role_id'],
            'p_perm_id' => $params['perm_id']
        );
        $query = "INSERT INTO role_perm (role_id, perm_id) VALUES(:p_role_id, :p_perm_id)";
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

    private function db_removeAllPermissionsFromRole($params)
    {
        $input = array(
            'p_id' => $params['role_id']
        );
        $query = "DELETE from role_perm where role_id = :p_id";
        $result = $this->db->query($query, $input);
        return $result;
    }

    private function db_createRole($params)
    {
        $input = array(
            'p_title' => $params['title']
        );
        $query = "INSERT INTO roles (title) VALUES(:p_title)";
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

    private function db_removeUserCouponTypes($params)
    {
        $input = array(
            'p_id' => $params['id']
        );
        $query = "DELETE from user_coupon_types where user_id = :p_id";
        $result = $this->db->query($query, $input);
        return $result;
    }

    private function db_removeUser($params)
    {
        $input = array(
            'p_id' => $params['id']
        );
        $query = "DELETE from users where id = :p_id";
        $result = $this->db->query($query, $input);
        return $result;
    }

    private function db_removeRole($params)
    {
        $input = array(
            'p_id' => $params['id']
        );
        $query = "DELETE from roles where id = :p_id";
        $result = $this->db->query($query, $input);
        return $result;
    }

    private function db_createPermission($params)
    {
        $input = array(
            'p_title' => $params['title'],
            'p_is_backend' => $params['is_backend'],
            'p_module_id' => $params['module_id'],
        );
        $query = "INSERT INTO permissions (title, is_backend, module_id) VALUES(:p_title, :p_is_backend, :p_module_id)";

        if(isset($params['id'])) {
            $input['p_id'] = $params['id'];

            $query = "UPDATE permissions SET title = :p_title, is_backend = :p_is_backend, module_id = :p_module_id WHERE id = :p_id";
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

    private function db_removePermission($params)
    {
        $input = array(
            'p_id' => $params['id']
        );
        $query = "DELETE from permissions where id = :p_id";
        $result = $this->db->query($query, $input);
        return $result;
    }

    private function db_createModule($params)
    {
        $input = array(
            'p_title' => $params['title']
        );
        $query = "INSERT INTO modules (title) VALUES(:p_title)";
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

    private function db_removeModule($params)
    {
        $input = array(
            'p_id' => $params['id']
        );
        $query = "DELETE from modules where id = :p_id";
        $result = $this->db->query($query, $input);
        return $result;
    }


}