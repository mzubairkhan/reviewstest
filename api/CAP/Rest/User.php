<?php

namespace CAP\Rest;

use CAP\User\Manager as UserManager;
use GeoIp2\Database\Reader;
use CAP\Core\Common;
use CAP\AWSApi\Manager as AWSApiManager;
use CAP\Network\NetworkAcl\Manager as NetworkAclManager;


class User {

    function __construct()
    {
    }


    /**
     * get User
     *
     * @return array
     * @access public
     * @url GET get
     */
    function get()
    {
        $retObj = array();

        try {

            if ( !empty($_GET['user_id']) ) {

                $params = array(
                    'user_id' => $_GET['user_id']
                );
                $userObj = new UserManager();
                $retObj = $userObj->get($params);

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
     * get User
     *
     * @return array
     * @access public
     * @url GET getUser
     */
    function getUser()
    {
        $retObj = array();

        try {

            if ( !empty($_GET['user_id']) ) {

                $params = array(
                    'user_id' => $_GET['user_id']
                );
                $userObj = new UserManager();
                $retObj = $userObj->getUser($params);

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
     * get All Users data
     *
     * @return array
     * @access public
     * @url GET getAll
     */
    function getAll()
    {
        $retObj = array();

        try {

            $userObj = new UserManager();
            $retObj = $userObj->getUsers();

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
     * Create User
     *
     * @return array
     * @access protected
     * @url POST create
     */
    function create()
    {
        $retObj = array();

        try {

            if ( !empty($_POST['username'])
                && !empty($_POST['password'] )
            ) {

                $user = $_POST;
                $userObj = new UserManager();
                $response = $userObj->insertUser($user);
                $retObj = $response;

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
     * Update User
     *
     * @return array
     * @access protected
     * @url POST update
     */
    function update()
    {
        $retObj = array();

        try {

            if ( !empty($_POST['user_id'])
            ) {

                $user = $_POST;
                $userObj = new UserManager();
                $response = $userObj->updateUser($user);
                $retObj = $response;

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
     * authenticate user
     *
     * @return array
     * @access public
     * @url POST login
     */
    function login()
    {
        $retObj = array();

        try {

            if ( !empty($_POST['username'])
                && !empty($_POST['password'])
            ) {

                $user = array();
                $user['username'] = $_POST['username'];
                $user['password']  = md5($_POST['password']);

                $userObj = new UserManager();
                $response = $userObj->login($user);

                if(isset($response) && $response['count'] > 0) {

                    $userId = $response['id'];
                    $token = $userObj->getTokenByUserId($userId);

                    //$retObj['id']= $userId;
                    $retObj['token'] = $token.'-'.$userId.'-'.$response['is_admin'];

                } else {
                    throw new \Exception('Validation failed');
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
     * test
     *
     * @return array
     * @access public
     * @url GET test
     */
    function test()
    {
        $retObj = array();

        try {
            // This creates the Reader object, which should be reused across
            // lookups.
            $settings = Common::getSettings();
            $maxmind_db_path = $settings['maxmind']['path'];

            $reader = new Reader($maxmind_db_path);

            // Replace "city" with the appropriate method for your database, e.g.,
            // "country".
            $record = $reader->city('128.101.101.101');

            print($record->country->isoCode . "\n"); // 'US'
            print($record->country->name . "\n"); // 'United States'
            print($record->country->names['zh-CN'] . "\n"); // '美国'

            print($record->mostSpecificSubdivision->name . "\n"); // 'Minnesota'
            print($record->mostSpecificSubdivision->isoCode . "\n"); // 'MN'

            print($record->city->name . "\n"); // 'Minneapolis'

            print($record->postal->code . "\n"); // '55455'

            print($record->location->latitude . "\n"); // 44.9733
            print($record->location->longitude . "\n"); // -93.2323


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
     * test
     *
     * @return array
     * @access public
     * @url GET testinvoice
     */
    function testinvoice()
    {
        $retObj = array();

        try {
            $obj= new AWSApiManager();
            $retObj = $obj->getList();


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
     * Add IP
     *
     * @return array
     * @access protected
     * @url POST addIP
     */
    function addIP()
    {
        $retObj = array();
        try {

            if (isset($_POST['ip']) && isset($_POST['user_id']))
            {

                $params = array(
                    'ip' => $_POST['ip'],
                    'user_id' => $_POST['user_id'],
                    'notes' => ($_POST['notes']) ? $_POST['notes'] : '',
                    'allowInRDS' => ($_POST['allowInRDS'])
                );

                if(!filter_var($params['ip'], FILTER_VALIDATE_IP))
                {
                    throw new \Exception('Error: Please enter a valid IP address.');
                }


                if(isset($_POST['id'])) {
                    $params['id'] = $_POST['id'];
                }

                $userObj = new UserManager();
                $retObj = $userObj->addIP($params);


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
     * get User IPs
     *
     * @return array
     * @access protected
     * @url GET getIPs
     */
    function getIPs()
    {
        $retObj = array();

        try {
            $params = array();
            $userObj = new UserManager();
            $retObj = $userObj->getIPs($params);


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
     * get User IP
     *
     * @return array
     * @access protected
     * @url GET getIP
     */
    function getIP()
    {
        $retObj = array();

        try {
            $params = array(
                'id' => $_GET['id']
            );
            $userObj = new UserManager();
            $retObj = $userObj->getIP($params);


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
     * Remove IP
     *
     * @return array
     * @access protected
     * @url POST removeIP
     */
    function removeIP()
    {
        $retObj = array();
        try {

            if (isset($_POST['id']))
            {

                $params = array(
                    'id' => $_POST['id']
                );

                $retObj['removed'] = false;

                $userObj = new UserManager();
                if($userObj->removeIP($params)) {
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
     * get User IPs
     *
     * @return array
     * @access public
     * @url GET getRDSIPs
     */
    function getRDSIPs()
    {
        $retObj = array();
        try{

            $Obj = new NetworkAclManager();
            $retObj = $Obj->getIPsAssociatedWithDBInstance();

        }catch (\Exception $e) {

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