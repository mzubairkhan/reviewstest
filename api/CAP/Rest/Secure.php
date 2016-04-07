<?php

namespace CAP\Rest;

use CAP\Secure\Manager as SecureManager;

class Secure {

    function __construct()
    {
    }

    /**
     * auto Sign In reseller
     *
     * @return array
     * @access protected
     * @url POST resellerSignIn
     */
    function resellerSignIn()
    {

        $retObj = array();
        try {

            if(isset($_POST['username'])) {

                $params = array(
                    'username' => $_POST['username']
                );

                $obj = new SecureManager();
                // check if reseller exists
                if($obj->resellerExists($params)) {
                    $retObj = $obj->getReseller($params);
                } else {
                    $retObj = $obj->createReseller($params);
                }
            } else {
                throw new \Exception('Invalid output');
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