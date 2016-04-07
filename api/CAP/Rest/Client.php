<?php

namespace CAP\Rest;

use CAP\MailchimpAPI\Manager as MailChimpManager;
use CAP\Core\Common;
use Symfony\Component\Config\Definition\Exception\Exception;
use CAP\WHMCS\Client\Manager as WHMCSClientManager;
use CAP\Process\Manager as ProcessManager;


class Client {

    function __construct()
    {
    }


    /**
     * get unpaid clients
     *
     * @return array
     * @access public
     * @url GET getUnPaidClients
     */
    function getUnPaidClients()
    {
        $retObj = array();
        try {

            if (!empty($_GET['date'])
            ) {

                $params = array();
                $params['date'] = $_GET['date'];

                $clientObj = new WHMCSClientManager();
                $response = $clientObj->getClientsWhoHaveNotPaid($params);
                $users = array();

                // grouping
                foreach($response as $item)
                {
                    if(!isset($users[$item['clientid']])) {
                       $users[$item['clientid']][] = $item;
                    }

                }

                // filtering
                $result = array();
                foreach($users as $item) {
                    $cnt = 0;
                    foreach($item as $inv) {
                        if($inv['status'] != 'Unpaid') {
                            $cnt++;
                        }
                    }
                    if($cnt == 0)
                        $result[] = $item[0];
                }
                $data = $result;

                $contents = null;
                $delimiter = ',';
                $enclosure = '"';
                $handle = fopen('php://temp', 'r+');
                foreach ($data as $line) {
                    fputcsv($handle, $line, $delimiter, $enclosure);
                }
                rewind($handle);
                while (!feof($handle)) {
                    $contents .= fread($handle, 8192);
                }
                fclose($handle);
                echo $contents; die();

                if(isset($response)) {
                    $retObj = $result;
                } else {
                    throw new \Exception('Invalid output');
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
     * get expired clients who have not renewed
     *
     * @return array
     * @access protected
     * @url GET getExpiredClients
     */
    function getExpiredClients()
    {
       $retObj = array();
       try {

            if (!empty($_GET['date'])
            ) {

                $params = array();
                $params['date'] = $_GET['date'];

                $clientObj = new WHMCSClientManager();
                $response = $clientObj->getClientsWhoHaveNotRenewed($params);

                $params = array('clientid','email' ,'invoiceid','userid','invoicetotal','invoiceitemamount','hostingamount' ,'billingcycle','paymentmethod');
                $response = $clientObj->filterResultSetByParams($response , $params);

                if(isset($response)) {
                    $retObj = $response;
                } else {
                    throw new \Exception('Invalid output');
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
     * get expired clients who are eligible for discount
     *
     * @return array
     * @access protected
     * @url GET getClientsEligibleForRenewalDiscount
     */
    function getClientsEligibleForRenewalDiscount()
    {
        $retObj = array();
        try {

            if (!empty($_GET['date'])
            ) {

                $discount = Common::getDiscountValuesForRenewal();

                $params = array();
                $params['date'] = $_GET['date'];
                $params['discount'] = $discount;

                $clientObj = new WHMCSClientManager();
                $clientList = $clientObj->getClientsWhoHaveNotRenewed($params);

                if(isset($clientList) && count($clientList) > 0) {
                    $procObj = new ProcessManager();
                    $response = $procObj->filterListForDiscountedClients($clientList, $params['discount']);

                    if(isset($response)) {
                        $retObj = $response;
                    } else {
                        throw new \Exception('Invalid output');
                    }

                } else {
                    throw new \Exception('Invalid output');
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
     * unsubscribe customer from mailchimp list who has paid invoice
     *
     * @return array
     * @access public
     * @url POST unsubscribeClientFromMailChimpList
     */
    function unsubscribeClientFromMailChimpList()
    {
        $retObj = array();
        try {

            if (!empty($_POST['email'])
            ) {

                $member = array();
                $member['email'] = $_POST['email'];

                $settings = Common::getSettings();
                $listid = $settings['mailchimp']['list_renewal_id'];

                try {
                    $mailchimpObj = new MailChimpManager();
                    $response = $mailchimpObj->unsubscribeMemberFromList($member , $listid);

                    if($response) {
                        $retObj = array('unsubscribed' => true);
                    } else {
                        throw new \Exception('Error unsubscribing client from mail list.');
                    }


                } catch(Exception $e) {
                    throw new \Exception($e->getMessage());
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
     * subscribe customer from mailchimp list who has paid invoice
     *
     * @return array
     * @access public
     * @url POST subscribeClientToMailChimpList
     */
    function subscribeClientToMailChimpList()
    {
        $retObj = array();
        try {

            if (!empty($_POST['email'])
            ) {

                $member = array();
                $member['email'] = array('email' => $_POST['email']);
                $member['merge_vars'] = array(
                    'FNAME'=>'gmail', 'LNAME'=>'CC'
                );

                $settings = Common::getSettings();
                $listid = $settings['mailchimp']['list_renewal']['default'];

                try {
                    $mailchimpObj = new MailChimpManager();
                    $response = $mailchimpObj->subscribeMemberToList($member , $listid);

                    if($response) {
                        $retObj = array('subscribed' => true);
                    } else {
                        throw new \Exception('Error subscribing client from mail list.');
                    }


                } catch(Exception $e) {
                    throw new \Exception($e->getMessage());
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
     * subscribe customers from mailchimp list who has paid invoice
     *
     * @return array
     * @access public
     * @url GET subscribeClientsToMailChimpList
     */
    function subscribeClientsToMailChimpList()
    {
        $retObj = array();
        try {

            if ( !empty($_GET['email1']) && !empty($_GET['email2'])
            ) {

                $members = array();
                $member1['email'] = array('email' => $_GET['email1']);
                $member1['merge_vars'] = array(
                    'FNAME'=>'gmail', 'LNAME'=>'CC'
                );
                $member2['email'] = array('email' => $_GET['email2']);
                $member2['merge_vars'] = array(
                    'FNAME'=>'gmail', 'LNAME'=>'CC'
                );
                $members[] = $member1;
                $members[] = $member2;

                $settings = Common::getSettings();
                $listid = $settings['mailchimp']['list_renewal']['default'];

                try {
                    $mailchimpObj = new MailChimpManager();
                    $response = $mailchimpObj->subscribeMembersToList($listid, $members);

                    if($response) {
                        $retObj = array('subscribed' => true);
                    }


                } catch(Exception $e) {
                    $params_email = array('action_date' => date('Y-m-d H:i:s') , 'reason' => 'C'.$e->getMessage());
                    Common::sendEmailIfCampaignNotRan($params_email);
                    throw new \Exception($e->getMessage());
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

}