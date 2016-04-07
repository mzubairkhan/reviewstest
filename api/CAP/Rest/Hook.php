<?php

namespace CAP\Rest;

use CAP\Process\Manager as ProcessManager;
use CAP\Core\Common;

class Hook {

    function __construct()
    {
    }

    /**
     * process for applying discount to expired customers and email them
     *
     * @return array
     * @access public
     * @url GET customerPaidInvoiceAfterDiscount
     */
    function customerPaidInvoiceAfterDiscount()
    {
        $retObj = array();
        try {

            if (isset($_GET['invoiceid']) && isset($_GET['campaign_type']))
            {

                $params = array(
                    'invoiceid' => $_GET['invoiceid']
                );

                $campaign_type = $_GET['campaign_type'];

                $settings = Common::getSettings();
                $mChimpLists = $settings['mailchimp']['list_'.$campaign_type];
                //$mChimpListId = $mChimpLists['default'];
                $params['lists'] = $mChimpLists;

                switch($campaign_type) {
                    case Constants::CAMPAIGN_TYPE_RENEWAL:
                        $params['campaign_type'] = Constants::CAMPAIGN_TYPE_RENEWAL;
                        break;
                    case Constants::CAMPAIGN_TYPE_RENEWAL_1:
                        $params['campaign_type'] = Constants::CAMPAIGN_TYPE_RENEWAL_1;
                        break;
                    default:
                        throw new \Exception('Unsupported campaign type');
                }

                $obj = new ProcessManager();
                if($obj->customerPaidInvoiceAfterDiscount($params)) {
                    $retObj['completed'] = true;
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
     * calculate CLT for a specific client
     *
     * @return array
     * @access protected
     * @url GET calculateCLTforClient
     */
    function calculateCLTforClient()
    {
        $retObj = array();
        try {

            $obj = new ProcessManager();
            $params = array(
                'userid' => $_GET['userid']
            );
            if($obj->calculateCLTforClient($params)) {
                $retObj['completed'] = true;
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
     * calculate CLT for a specific client improved
     *
     * @return array
     * @access public
     * @url GET calculateCLTforClientImproved
     */
    function calculateCLTforClientImproved()
    {
        $retObj = array();
        try {

            $obj = new ProcessManager();
            $params = array(
                'invoice_id' => $_GET['invoice_id']
            );
            if($obj->calculateCLTforClientImproved($params)) {
                $retObj['completed'] = true;
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