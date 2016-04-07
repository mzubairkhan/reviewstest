<?php

namespace CAP\Rest;

use CAP\Core\Common;
use CAP\User\Manager as UserManager;
use CAP\Acl\Manager as AclManager;

class Acl {

    function __construct()
    {
    }
    /**
     * get User Permissions
     *
     * @return array
     * @access public
     * @url GET getUserPermissions
     */
    function getUserPermissions()
    {
        $retObj = array();
        try {

            if (isset($_REQUEST['user_id']) && isset($_REQUEST['is_backend']))
            {
                $retObj['allowed'] = false;

                $params = array(
                    'user_id' => $_REQUEST['user_id'],
                    'is_backend' => $_REQUEST['is_backend']
                );

                $aclObj = new AclManager();
                $permissions = $aclObj->getUserPermissions($params);
                $roles = $aclObj->getUserRoles($params);
                $retObj = array(
                    'permissions' => $permissions,
                    'roles' => $roles
                );

            } else {

                throw new \Exception('Unsupported Request: missing critical parameter(s).');

            }

        } catch (\Exception $e) {

            Api::invalidResponse(
                $e->getMessage(),
                400,
                Constants::STATUS_INVALID,
                $e,
                true,
                true
            );

        }

        return Api::apiResponse($retObj, Constants::STATUS_SUCCESSFUL);
    }


    /**
     * Is User allowed permission
     *
     * @return array
     * @access protected
     * @url GET isAllowedPermission
     */
    function isAllowedPermission()
    {
        $retObj = array();
        try {

            if (isset($_REQUEST['user_id']) && isset($_REQUEST['is_backend']) && isset($_REQUEST['method']))
            {
                $retObj['allowed'] = false;

                $params = array(
                    'user_id' => $_REQUEST['user_id'],
                    'is_backend' => $_REQUEST['is_backend'],
                    'method' => $_REQUEST['method']
                );

                $aclObj = new AclManager();
                $permissions = $aclObj->getUserPermissions($params);

                //c. check if method valid
                foreach($permissions as $permission)
                {
                    if($permission['title'] == $params['method']) {
                        $retObj['allowed'] = true;
                    }
                }

            } else {

                throw new \Exception('Unsupported Request: missing critical parameter(s).');

            }

        } catch (\Exception $e) {

            Api::invalidResponse(
                $e->getMessage(),
                400,
                Constants::STATUS_INVALID,
                $e,
                true,
                true
            );

        }

        return Api::apiResponse($retObj, Constants::STATUS_SUCCESSFUL);
    }

    /**
     * Remove User
     *
     * @return array
     * @access protected
     * @url POST removeUser
     */
    function removeUser()
    {
        $retObj = array();
        try {

            if (isset($_POST['id']))
            {

                $params = array(
                    'id' => $_POST['id']
                );

                $retObj['removed'] = false;

                $objAcl = new AclManager();
                if($objAcl->removeUser($params)) {
                    $retObj['removed'] = true;
                }
            } else {

                throw new \Exception('Unsupported Request: missing critical parameter(s).');

            }


        } catch (\Exception $e) {

            Api::invalidResponse(
                $e->getMessage(),
                400,
                Constants::STATUS_INVALID,
                $e,
                true,
                true
            );

        }

        return Api::apiResponse($retObj, Constants::STATUS_SUCCESSFUL);
    }

    /**
     * Create Role
     *
     * @return array
     * @access protected
     * @url POST createRole
     */
    function createRole()
    {
        $retObj = array();
        try {

            if (isset($_POST['title']))
            {

                $params = array(
                    'title' => $_POST['title']
                );

                $objAcl = new AclManager();
                $retObj = $objAcl->createRole($params);

            } else {

                throw new \Exception('Unsupported Request: missing critical parameter(s).');

            }


        } catch (\Exception $e) {

            Api::invalidResponse(
                $e->getMessage(),
                400,
                Constants::STATUS_INVALID,
                $e,
                true,
                true
            );

        }

        return Api::apiResponse($retObj, Constants::STATUS_SUCCESSFUL);
    }

    /**
     * Remove Role
     *
     * @return array
     * @access protected
     * @url POST removeRole
     */
    function removeRole()
    {
        $retObj = array();
        try {

            if (isset($_POST['id']))
            {

                $params = array(
                    'id' => $_POST['id']
                );

                $retObj['removed'] = false;

                $objAcl = new AclManager();
                if($objAcl->removeRole($params)) {
                    $retObj['removed'] = true;
                }
            } else {

                throw new \Exception('Unsupported Request: missing critical parameter(s).');

            }


        } catch (\Exception $e) {

            Api::invalidResponse(
                $e->getMessage(),
                400,
                Constants::STATUS_INVALID,
                $e,
                true,
                true
            );

        }

        return Api::apiResponse($retObj, Constants::STATUS_SUCCESSFUL);
    }

    /**
     * Create Permission
     *
     * @return array
     * @access protected
     * @url POST createPermission
     */
    function createPermission()
    {
        $retObj = array();
        try {

            if (isset($_POST['title']))
            {

                $params = array(
                    'title' => $_POST['title'],
                    'is_backend' => $_POST['is_backend'],
                    'module_id' => $_POST['module_id']
                );

                if(isset($_POST['id'])) {
                    $params['id'] = $_POST['id'];
                }

                $objAcl = new AclManager();
                $retObj = $objAcl->createPermission($params);

            } else {

                throw new \Exception('Unsupported Request: missing critical parameter(s).');

            }


        } catch (\Exception $e) {

            Api::invalidResponse(
                $e->getMessage(),
                400,
                Constants::STATUS_INVALID,
                $e,
                true,
                true
            );

        }

        return Api::apiResponse($retObj, Constants::STATUS_SUCCESSFUL);
    }

    /**
     * Remove Permission
     *
     * @return array
     * @access protected
     * @url POST removePermission
     */
    function removePermission()
    {
        $retObj = array();
        try {

            if (isset($_POST['id']))
            {

                $params = array(
                    'id' => $_POST['id']
                );

                $retObj['removed'] = false;

                $objAcl = new AclManager();
                if($objAcl->removePermission($params)) {
                    $retObj['removed'] = true;
                }
            } else {

                throw new \Exception('Unsupported Request: missing critical parameter(s).');

            }


        } catch (\Exception $e) {

            Api::invalidResponse(
                $e->getMessage(),
                400,
                Constants::STATUS_INVALID,
                $e,
                true,
                true
            );

        }

        return Api::apiResponse($retObj, Constants::STATUS_SUCCESSFUL);
    }

    /**
     * Create Module
     *
     * @return array
     * @access protected
     * @url POST createModule
     */
    function createModule()
    {
        $retObj = array();
        try {

            if (isset($_POST['title']))
            {

                $params = array(
                    'title' => $_POST['title']
                );

                $objAcl = new AclManager();
                $retObj = $objAcl->createModule($params);

            } else {

                throw new \Exception('Unsupported Request: missing critical parameter(s).');

            }


        } catch (\Exception $e) {

            Api::invalidResponse(
                $e->getMessage(),
                400,
                Constants::STATUS_INVALID,
                $e,
                true,
                true
            );

        }

        return Api::apiResponse($retObj, Constants::STATUS_SUCCESSFUL);
    }

    /**
     * Remove Module
     *
     * @return array
     * @access protected
     * @url POST removeModule
     */
    function removeModule()
    {
        $retObj = array();
        try {

            if (isset($_POST['id']))
            {

                $params = array(
                    'id' => $_POST['id']
                );

                $retObj['removed'] = false;

                $objAcl = new AclManager();
                if($objAcl->removeModule($params)) {
                    $retObj['removed'] = true;
                }
            } else {

                throw new \Exception('Unsupported Request: missing critical parameter(s).');

            }


        } catch (\Exception $e) {

            Api::invalidResponse(
                $e->getMessage(),
                400,
                Constants::STATUS_INVALID,
                $e,
                true,
                true
            );

        }

        return Api::apiResponse($retObj, Constants::STATUS_SUCCESSFUL);
    }

    /**
     * Associate permissions to Role
     *
     * @return array
     * @access protected
     * @url POST associatePermissionsToRole
     */
    function associatePermissionsToRole()
    {
        $retObj = array();
        try {

            if (isset($_POST['role_id']))
            {
                $permission_ids = json_decode($_POST['permission_ids']);
                $params = array(
                    'role_id' => $_POST['role_id'],
                    'permission_ids' => $permission_ids
                );

                $objAcl = new AclManager();
                $retObj['completed'] = false;
                $response = $objAcl->associatePermissionsToRole($params);
                $retObj['completed'] = true;

            } else {

                throw new \Exception('Unsupported Request: missing critical parameter(s).');

            }


        } catch (\Exception $e) {

            Api::invalidResponse(
                $e->getMessage(),
                400,
                Constants::STATUS_INVALID,
                $e,
                true,
                true
            );

        }

        return Api::apiResponse($retObj, Constants::STATUS_SUCCESSFUL);
    }

    /**
     * Associate roles to User
     *
     * @return array
     * @access protected
     * @url POST associateRolesToUser
     */
    function associateRolesToUser()
    {
        $retObj = array();
        try {

            if (isset($_POST['user_id']))
            {
                $role_ids = json_decode($_POST['role_ids']);
                $params = array(
                    'user_id' => $_POST['user_id'],
                    'role_ids' => $role_ids
                );

                $objAcl = new AclManager();
                $retObj['completed'] = false;
                $response = $objAcl->associateRolesToUser($params);
                $retObj['completed'] = true;

            } else {

                throw new \Exception('Unsupported Request: missing critical parameter(s).');

            }


        } catch (\Exception $e) {

            Api::invalidResponse(
                $e->getMessage(),
                400,
                Constants::STATUS_INVALID,
                $e,
                true,
                true
            );

        }

        return Api::apiResponse($retObj, Constants::STATUS_SUCCESSFUL);
    }

    /**
     * get User Roles
     *
     * @return array
     * @access protected
     * @url GET getUserRoles
     */
    function getUserRoles()
    {
        $retObj = array();
        try {

            if (isset($_GET['user_id']))
            {

                $params = array(
                    'user_id' => $_GET['user_id']
                );

                $objAcl = new AclManager();
                $userRoles = $objAcl->getUserRoles($params);
                $totalRoles = $objAcl->getRoles();
                $user = $objAcl->getUser($params);

                $retObj['userRoles'] = $userRoles;
                $retObj['totalRoles'] = $totalRoles;
                $retObj['user'] = $user;

            } else {

                throw new \Exception('Unsupported Request: missing critical parameter(s).');

            }


        } catch (\Exception $e) {

            Api::invalidResponse(
                $e->getMessage(),
                400,
                Constants::STATUS_INVALID,
                $e,
                true,
                true
            );

        }

        return Api::apiResponse($retObj, Constants::STATUS_SUCCESSFUL);
    }

    /**
     * get Permissions Role
     *
     * @return array
     * @access protected
     * @url GET getPermissionsRole
     */
    function getPermissionsRole()
    {
        $retObj = array();
        try {

            if (isset($_GET['permission_id']))
            {

                $params = array(
                    'permission_id' => $_GET['permission_id']
                );

                $objAcl = new AclManager();
                $rolePermissions = $objAcl->getPermissionsRole($params);
                $permission = $objAcl->getPermission($params);

                $retObj['permissionsRole'] = $rolePermissions;
                $retObj['permission'] = $permission;


            } else {

                throw new \Exception('Unsupported Request: missing critical parameter(s).');

            }


        } catch (\Exception $e) {

            Api::invalidResponse(
                $e->getMessage(),
                400,
                Constants::STATUS_INVALID,
                $e,
                true,
                true
            );

        }

        return Api::apiResponse($retObj, Constants::STATUS_SUCCESSFUL);
    }


    /**
     * get Roles Permissions
     *
     * @return array
     * @access protected
     * @url GET getRolePermissions
     */
    function getRolePermissions()
    {
        $retObj = array();
        try {

            if (isset($_GET['role_id']))
            {

                $params = array(
                    'role_id' => $_GET['role_id']
                );

                $objAcl = new AclManager();
                $rolePermissions = $objAcl->getRolePermissions($params);
                $totalPermissions = $objAcl->getAllModulePermissions();
                $role = $objAcl->getRole($params);

                $retObj['rolePermissions'] = $rolePermissions;
                $retObj['totalPermissions'] = $totalPermissions;
                $retObj['role'] = $role;


            } else {

                throw new \Exception('Unsupported Request: missing critical parameter(s).');

            }


        } catch (\Exception $e) {

            Api::invalidResponse(
                $e->getMessage(),
                400,
                Constants::STATUS_INVALID,
                $e,
                true,
                true
            );

        }

        return Api::apiResponse($retObj, Constants::STATUS_SUCCESSFUL);
    }

    /**
     * get Module Permissions
     *
     * @return array
     * @access protected
     * @url GET getModulePermissions
     */
    function getModulePermissions()
    {
        $retObj = array();
        try {

            if (isset($_GET['module_id']))
            {

                $params = array(
                    'module_id' => $_GET['module_id']
                );

                $objAcl = new AclManager();
                $modulePermissions = $objAcl->getModulePermissions($params);
                $module = $objAcl->getModule($params);

                $retObj['modulePermissions'] = $modulePermissions;
                $retObj['module'] = $module;

            } else {

                throw new \Exception('Unsupported Request: missing critical parameter(s).');

            }


        } catch (\Exception $e) {

            Api::invalidResponse(
                $e->getMessage(),
                400,
                Constants::STATUS_INVALID,
                $e,
                true,
                true
            );

        }

        return Api::apiResponse($retObj, Constants::STATUS_SUCCESSFUL);
    }


    /**
     * get All Roles
     *
     * @return array
     * @access protected
     * @url GET getRoles
     */
    function getRoles()
    {
        $retObj = array();
        try {

            $objAcl = new AclManager();
            $retObj = $objAcl->getRoles();


        } catch (\Exception $e) {

            Api::invalidResponse(
                $e->getMessage(),
                400,
                Constants::STATUS_INVALID,
                $e,
                true,
                true
            );

        }

        return Api::apiResponse($retObj, Constants::STATUS_SUCCESSFUL);
    }

    /**
     * get All Permissions
     *
     * @return array
     * @access protected
     * @url GET getPermissions
     */
    function getPermissions()
    {
        $retObj = array();
        try {

            $objAcl = new AclManager();
            $retObj = $objAcl->getPermissions();


        } catch (\Exception $e) {

            Api::invalidResponse(
                $e->getMessage(),
                400,
                Constants::STATUS_INVALID,
                $e,
                true,
                true
            );

        }

        return Api::apiResponse($retObj, Constants::STATUS_SUCCESSFUL);
    }

    /**
     * get All Modules
     *
     * @return array
     * @access protected
     * @url GET getModules
     */
    function getModules()
    {
        $retObj = array();
        try {

            $objAcl = new AclManager();
            $retObj = $objAcl->getModules();


        } catch (\Exception $e) {

            Api::invalidResponse(
                $e->getMessage(),
                400,
                Constants::STATUS_INVALID,
                $e,
                true,
                true
            );

        }

        return Api::apiResponse($retObj, Constants::STATUS_SUCCESSFUL);
    }


    /**
     * get Permission
     *
     * @return array
     * @access protected
     * @url GET getPermission
     */
    function getPermission()
    {
        $retObj = array();
        try {
            if (isset($_GET['permission_id']))
            {

                $params = array(
                    'permission_id' => $_GET['permission_id']
                );

                $objAcl = new AclManager();
                $permission = $objAcl->getPermission($params);
                $retObj = $permission;

            } else {

                throw new \Exception('Unsupported Request: missing critical parameter(s).');

            }


        } catch (\Exception $e) {

            Api::invalidResponse(
                $e->getMessage(),
                400,
                Constants::STATUS_INVALID,
                $e,
                true,
                true
            );

        }

        return Api::apiResponse($retObj, Constants::STATUS_SUCCESSFUL);
    }

}