<?php

namespace CAP\Rest;


class Ping {

    function __construct()
    {
    }

    /**
     *
     * @return array
     * @access public
     * @url GET test
     */
    function test()
    {

        $retObj = array();

        return Api::apiResponse($retObj, Constants::STATUS_SUCCESSFUL);
    }


}