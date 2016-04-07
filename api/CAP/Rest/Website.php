<?php

namespace CAP\Rest;

use Aws\Common\Enum\Region;
use CAP\Website\Manager as WebsiteManager;
use CAP\Core\Common;
use CAP\AWSApi\Manager as AWSApi;


class Website {

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

            $obj = new WebsiteManager();
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

    /**
     * Create website
     *
     * @return array
     * @access public
     * @url POST createWebsite
     */

    function createWebsite(){
        $retObj = array();
        try {
            if(!empty($_POST['title']) && !empty($_POST['domain'])) {
                if(!empty($_FILES["website_logo"]["type"])) {

                    $slug = Common::slugify($_POST['title']);
                    $file = Common::file_upload_handler($_FILES, 'website_logo', $slug);
                    $setting = Common::getSettings();
                    $sBucket = $setting['aws-s3']['s3_bucket_name'] . '/apps';
                    $sKeyname = $file['file_name'];
                    $file_path = $file['file_path'];
                    $aws_region = Region::VIRGINIA;
                    $params_aws = array(
                        'region' => $aws_region
                    );
                    $ab = new AWSApi($params_aws);
                    $link = $ab->s3UploadFileFromPath($sBucket,$sKeyname, $file_path );
                    $_POST['theme']['logo'] = $sKeyname;

                    if(isset($link['ObjectURL'])) {
                        unlink($file['file_path']);
                        $_POST['provider_logo'] = $sKeyname;
                    }
                }
                if($_POST['theme']['logo'] == '') {
                    unset($_POST['theme']['logo']);
                }
                $data = $_POST;
                $obj = new WebsiteManager();
                $response = $obj->createWebsite($data);
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
     * Update website
     *
     * @return array
     * @access public
     * @url POST updateWebsite
     */

    function updateWebsite(){


        $retObj = array();
        try {
            if(!empty($_POST['website_id']) && !empty($_POST['domain']) && !empty($_POST['title'])) {

                if(!empty($_FILES["website_logo"]["type"])) {
                    $slug = Common::slugify($_POST['title']);
                    $file = Common::file_upload_handler($_FILES, 'website_logo', $slug);
                    $setting = Common::getSettings();
                    $sBucket = $setting['aws-s3']['s3_bucket_name'] . '/apps';
                    $sKeyname = $file['file_name'];
                    $file_path = $file['file_path'];
                    $aws_region = Region::VIRGINIA;
                    $params_aws = array(
                        'region' => $aws_region
                    );
                    $ab = new AWSApi($params_aws);
                    $link = $ab->s3UploadFileFromPath($sBucket,$sKeyname, $file_path );
                    if(isset($link['ObjectURL'])) {
                        unlink($file['file_path']);
                        $_POST['theme']['logo'] = $sKeyname;
                    }

                }
                if($_POST['theme']['logo'] == '') {
                    unset($_POST['theme']['logo']);
                }

                $data = $_POST;
                $obj = new WebsiteManager();
                $response = $obj->updateWebsite($data);
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
     * Get website by ID
     *
     * @return array
     * @access public
     * @url GET getWebsite
     */

    function getWebsite($website_id){


        $retObj = array();
        try {
            if(!empty($_GET['website_id'])) {
                $data = $_GET['website_id'];
                $obj = new WebsiteManager();
                $response = $obj->getWebsite($data);
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
     * Remove website logo
     *
     * @return bool
     * @access public
     * @url GET removeLogo
     */

    function removeLogo(){
        $file = $_GET['file'];
        $retObj = array();
        try {
            if(Common::file_unlink($file)){
                $retObj = 'Success';
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