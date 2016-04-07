<?php

namespace CAP\Rest;

use Aws\Common\Enum\Region;
use CAP\Media\Manager as MediaManager;
use CAP\Core\Common;
use CAP\AWSApi\Manager as AWSApi;


class Media {

    function __construct()
    {

    }

    /**
     * upload
     *
     * @return array
     * @access public
     * @url POST upload
     */

    function upload(){

        $retObj = array();
        try {

            if(!empty($_FILES["file"]["type"])) {

                $slug = 'media_upload';
                $file = Common::file_upload_handler($_FILES, 'file', $slug);
                $setting = Common::getSettings();
                $sBucket = $setting['aws-s3']['s3_bucket_name'] . '/media';

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
                    /*$retObj = $sKeyname;*/
                }

                $obj = new MediaManager();
                $response = $obj->create($sKeyname);
                $retObj = $response;
            }

            else {

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
     * Get all media items
     *
     * @return array
     * @access public
     * @url GET getAll

     */
    function getAll() {
        $retObj = array();
        try {

            $obj = new MediaManager();
            $response = $obj->getAll(1);
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