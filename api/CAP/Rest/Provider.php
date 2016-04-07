<?php

namespace CAP\Rest;

use Aws\Common\Enum\Region;
use CAP\Provider\Manager as ProviderManager;
use CAP\Core\Common;
use CAP\AWSApi\Manager as AWSApi;


class Provider {

    function __construct()
    {
    }

    /**
     * list providers
     *
     * @return array
     * @access public
     * @url GET listAll
     */

     function listAll(){


        $retObj = array();
        try {

            $pageObj = new ProviderManager();
            $response = $pageObj->getAll();
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
     * Create Provider
     *
     * @return array
     * @access protected
     * @url POST create
     */
     function create()
    {
        $retObj = array();

        try {

            if ( !empty($_POST['title']) ) {

                if(!empty($_FILES["provider_logo"]["type"])) {

                    $slug = Common::slugify($_POST['title']);
                    $file = Common::file_upload_handler($_FILES, 'provider_logo', $slug);
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
                        $_POST['logo'] = $sKeyname;
                    }
                }

                if($_POST['logo'] == '') {
                    unset($_POST['logo']);
                }

                $provider = $_POST;
                $pageObj = new ProviderManager();
                $response = $pageObj->create($provider);
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
     * Update Provider
     *
     * @return array
     * @access protected
     * @url POST update
     */
     function update()
    {
        $retObj = array();

        try {

            if ( !empty($_POST['title']) ) {


                if(!empty($_FILES["provider_logo"]["type"])) {
                    $slug = Common::slugify($_POST['title']);
                    $file = Common::file_upload_handler($_FILES, 'provider_logo', $slug);
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
                        $_POST['logo'] = $sKeyname;
                    }
                }

                if($_POST['logo'] == '') {
                    unset($_POST['logo']);
                }

                $provider = $_POST;
                $pageObj = new ProviderManager();
                $response = $pageObj->update($provider);
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
     * Get provider by ID
     *
     * @return array
     * @access public
     * @param int $provider_id
     * @url GET get
     *
     */
     function get($provider_id)
    {
        $retObj = array();

        try {

            if ( !empty($provider_id) ) {

                $pageObj = new ProviderManager();
                $response = $pageObj->get($provider_id);
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
     * Remove Page
     *
     * @return null
     * @access public
     * @param int $provider_id
     * @url GET remove
     */

    function remove($provider_id) {

        $retObj = array();
        try {
            if(!empty($provider_id)) {
                $pageObj = new ProviderManager();
                $response = $pageObj->remove($provider_id);
                $retObj = $response;
            } else {
                throw new \Exception('Unsupported Request: missing critical parameter(s).');
            }
        }
        catch (\Exception $e) {
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
     * Remove provider logo
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