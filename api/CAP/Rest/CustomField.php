<?php

namespace CAP\Rest;

use CAP\CustomField\Manager as CustomFieldManager;
use CAP\Core\Common;


class CustomField {

    function __construct()
    {
    }


    /**
     * list fields Group
     *
     * @return array
     * @access public
     * @url GET listAll
     */

    function listAll(){


        $retObj = array();
        try {

            $obj = new CustomFieldManager();
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
     * Create field group
     *
     * @return array
     * @access public
     * @url POST createGroup
     */

     function createGroup(){


        $retObj = array();
        try {

            if(!empty($_POST['title'])) {
                $cfgroup = $_POST;
                $obj = new CustomFieldManager();
                $response = $obj->createGroup($cfgroup);
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
     * Create field
     *
     * @return array
     * @access public
     * @url POST createField
     */

     function createField(){


        $retObj = array();
        try {

            if(!empty($_POST['key'])) {
                $cfgroup = $_POST;
                $obj = new CustomFieldManager();
                $response = $obj->createField($cfgroup);
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
     * Update field group
     *
     * @return array
     * @access public
     * @url POST updateGroup
     */

     function updateGroup(){

        $retObj = array();
        try {

            if(!empty($_POST['title'])) {
                $cfgroup = $_POST;
                $pageObj = new CustomFieldManager();
                $response = $pageObj->updateGroup($cfgroup);
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
     * Update update Group Fields
     *
     * @return array
     * @access public
     * @url POST updateField
     */

     function updateField(){
        //print_r($_POST);exit;
        $retObj = array();
        try {

            if(!empty($_POST['field_id'])) {
                $data = $_POST;
                $obj = new CustomFieldManager();
                $response = $obj->updateField($data);
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
     * Remove Field
     *
     * @return array
     * @access public
     * @url GET removeFieldGroup
     */

     function removeFieldGroup($id){

        $retObj = array();
        try {

            if(!empty($id)) {

                $obj = new CustomFieldManager();
                $response = $obj->removeFieldGroup($id);
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
     * Remove Field Group By ID
     *
     * @return array
     * @access public
     * @url GET removeField
     */

     function removeField($id){

        $retObj = array();
        try {

            if(!empty($id)) {

                $obj = new CustomFieldManager();
                $response = $obj->removeField($id);
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
     * get field by ID
     *
     * @return array
     * @access public
     * @url GET getField
     */

     function getField($id){

        $retObj = array();
        try {

            $pageObj = new CustomFieldManager();
            $response = $pageObj->getField($id);
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
     * get field by ID
     *
     * @return array
     * @access public
     * @url GET getFieldGroup
     */

     function getFieldGroup($id){

        $retObj = array();
        try {

            $obj = new CustomFieldManager();
            $response = $obj->getFieldGroup($id);
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
     * get field by group ID
     *
     * @return array
     * @access public
     * @url GET getGroupFields
     */

     function getGroupFields($group_id, $orderby){
        $retObj = array();
        try {

            $obj = new CustomFieldManager();
            $response = $obj->getGroupFields($group_id, $orderby);
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
     * Get Field for module type
     *
     * @return array
     * @access public
     * @url GET getFieldsByModule
     */
     function getFieldsByModule()
    {
        //print_r("hi");exit;

        $retObj = array();

        try {

            if ( !empty($_GET['module_type']) ) {

                $retObj = $_GET;
                $pageObj = new CustomFieldManager();
                $response = $pageObj->getFieldsByModule($retObj);
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
     * Get Field value by key and rel
     *
     * @return array
     * @access public
     * @url GET getFieldValue
     */
     function getFieldValue()
    {


        $retObj = array();

        try {

            if ( !empty($_GET['rel_id']) ) {

                $retObj = $_GET;
                $pageObj = new CustomFieldManager();
                $response = $pageObj->getFieldValue($retObj);
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
     * Update Field for module type
     *
     * @return array
     * @access public
     * @url POST updateFieldByModule
     */
     function updateFieldByModule()
    {
        //print_r("hi");exit;

        $retObj = array();

        try {

            if ( !empty($_POST['module_type']) ) {

                $retObj = $_POST;
                $obj = new CustomFieldManager();
                $response = $obj->updateFieldByModule($retObj);
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


}