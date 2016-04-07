<?php

namespace CAP\Rest;

use Aws\Common\Enum\Region;
use CAP\GitRepo\Manager as GitRepoManager;
use CAP\Core\Common;


class GitRepo {

    function __construct()
    {

    }

    /**
     * list websites
     *
     * @return array
     * @access public
     * @url GET listAll
     */

    function listAll(){

        $retObj = array();
        try {

            $obj = new GitRepoManager();
            $response = $obj->listAll();
            $retObj = $response;


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


    function pull(){
        $target = $_GET['target'] . '/GIT/pull.php';
        // create curl resource


        $retObj = array();
        try {

            $response = Common::curl_res($target);
            $retObj = $response;

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