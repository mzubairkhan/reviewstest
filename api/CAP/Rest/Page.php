<?php

namespace CAP\Rest;

use CAP\Page\Manager as PageManager;
use CAP\Core\Common;


class Page {

    function __construct()
    {
    }

    /**
     * Get user pages
     *
     * @return array
     * @access public
     * @url GET getPages
     */

     function getPages(){


        $retObj = array();
        try {

            $pageObj = new PageManager();
            $response = $pageObj->getPages();
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
     * Create Page
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

                $page = $_POST;
                $pageObj = new PageManager();
                $response = $pageObj->insertPage($page);
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
     * Update Page
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

                $page = $_POST;
                $pageObj = new PageManager();
                $response = $pageObj->updatePage($page);
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
     * Get Page by ID
     *
     * @return array
     * @access public
     * @param int $page_id
     * @url GET getPage
     *
     */
     function getPage($page_id)
    {
        $retObj = array();

        try {

            if ( !empty($page_id) ) {

                $pageObj = new PageManager();
                $response = $pageObj->getPage($page_id);
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
     * @param int $page_id
     * @url POST removePage
     */

    function removePage($page_id) {

        $retObj = array();
        try {
            if(!empty($page_id)) {
                $pageObj = new PageManager();
                $response = $pageObj->removePage($page_id);
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

}