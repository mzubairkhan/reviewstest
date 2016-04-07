<?php

namespace CAP\Process;

use CAP\Core\Common;
use CAP\Core\DB;
use CAP\Rest\Constants;

use CAP\MailchimpAPI\Manager as MailChimpManager;
use CAP\Retention\Manager as RetentionManager;
use CAP\WHMCS\Client\Manager as WHMCSClientManager;
use CAP\WHMCS\Invoice\Manager as WHMCSInvoiceManager;
use CAP\CLT\Manager as CLTManager;
use Symfony\Component\Config\Definition\Exception\Exception;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;

Class Manager {

    private $db;
    private $table;
    private $mChimpLists;
    private $mChimpFirstList;
    private $mChimpUnexperimentedList;

    function __construct()
    {
        $this->db = new DB('DB-BI');
        $this->table = 'users';
        $this->mChimpLists = array('A','B','C','D','E','F','G','H');
        $this->mChimpFirstList = 'A';
        $this->mChimpUnexperimentedList = 'Z';
    }

    public function campaignRenewal($params)
    {
        try {

            $retnObj = new RetentionManager();
            $executed = $retnObj->checkIfCampaignExecutedForRenewal($params);
            if($executed) {
                throw new \Exception('Campaign already ran for client who expired on '.$params['date']);
            }

            // 1. get Customers who are expired
            $clientObj = new WHMCSClientManager();
            $clientList = $clientObj->getClientsWhoHaveNotRenewed($params);

            if(isset($clientList) && count($clientList) > 0) {

                $filteredClients = $this->filterListForDiscountedClients($clientList, $params['discount']);

                $filteredClientsToBeExperimented = $filteredClients;

                // 1.1 check is_ab
                if($params['is_ab'] == 1) {

                    $segments = $params['segments'];

                    $filteredClientsToBeExperimented = array();
                    // perform a/b experimentation on list
                    $i = 0;
                    $j = 0;
                    foreach($filteredClients as $key => $client) {
                        if($i % $segments == 0) {
                            $j = 0;
                            $filteredClients[$key]['is_experimented'] = 0;
                            $filteredClients[$key]['tag'] = $this->mChimpUnexperimentedList;
                        } else {
                            $filteredClients[$key]['is_experimented'] = 1;
                            $filteredClients[$key]['tag'] = $this->mChimpLists[$j-1];
                            $filteredClientsToBeExperimented[$key] = $filteredClients[$key];
                        }
                        $j++;
                        $i++;

                    }
                }

                // 2. send those into funnel1 (expired users list)

                // preparing members to be inserted in funnel ($filteredClients)
                $membersForFunnel = $this->prepareMembersForFunnelInsertion($filteredClients, $params);

                $retnObj = new RetentionManager();
                foreach($membersForFunnel as $member)
                {
                    //inserting in funnel
                    $retnObj->addExpiredClientInFunnel($member);
                }

                // 3. apply discount to those customers ($filteredClientsToBeExperimented)

                foreach($filteredClientsToBeExperimented as $client)
                {
                    $invoiceObj = new WHMCSInvoiceManager();
                    $invoiceObj->applyDisCountOnRenewalInvoice($client, $params);
                }

                // 4. send email to those customers

                $settings = Common::getSettings();
                $lists = $settings['mailchimp']['list_renewal'];

                $filteredLists = $this->prepareMemberListForMailChimp($filteredClientsToBeExperimented);

                $m = new MailChimpManager();

                foreach($filteredLists as $listName => $filteredMembers) {
                    $emailed = $m->subscribeMembersToList($lists[$listName], $filteredMembers);
                }

                if($emailed) {
                    // all 4 steps successfully completed
                    return true;
                } else {
                    $params_email = array('action_date' => date('Y-m-d H:i:s') , 'reason' => 'B');
                    Common::sendEmailIfCampaignNotRan($params_email);
                }

            }

        } catch(\Exception $e) {
            $params_email = array('action_date' => date('Y-m-d H:i:s') , 'reason' => 'C'.$e->getMessage());
            Common::sendEmailIfCampaignNotRan($params_email);
            throw new \Exception($e->getMessage());
        }


    }

    public function campaignRenewal_1($params)
    {
        try {

            $retnObj = new RetentionManager();
            $executed = $retnObj->checkIfCampaignExecutedForRenewal($params);
            if($executed) {
                throw new \Exception('Campaign already ran for client who expired on '.$params['date']);
            }

            // 1. get Customers who are expired
            $clientObj = new WHMCSClientManager();
            $clientList = $clientObj->getClientsWhoHaveNotRenewed($params);

            if(isset($clientList) && count($clientList) > 0) {

                $filteredClients = $this->filterListForDiscountedClients($clientList, $params['discount']);


                $filteredClientsToBeExperimented = $filteredClients;

                $lists_breakdown = array(
                    'annual' => array(),
                    'monthly' => array(),
                );

                foreach($filteredClients as $key => $client)
                {
                    if($client['billingcycle'] == 'Annually') {
                        $lists_breakdown['annual'][$key] = $client;
                    } else if($client['billingcycle'] == 'Monthly') {
                        $lists_breakdown['monthly'][$key] = $client;
                    } else {
                        // unsetting other than monthly and annually
                        unset($filteredClients[$key]);
                    }
                }

                // 1.1 check is_ab
                if($params['is_ab'] == 1) {

                    $segments = $params['segments'];

                    $filteredClientsToBeExperimented = array();
                    // perform a/b experimentation on list
                    $i = 0;

                    foreach($lists_breakdown['annual'] as $key => $client)
                    {
                        $filteredClients[$key]['is_experimented'] = 1;
                        $filteredClients[$key]['tag'] = $this->mChimpLists[($i % ($segments/2))];
                        $filteredClientsToBeExperimented[$key] = $filteredClients[$key];
                        $i++;
                    }
                    $i = 0;

                    foreach($lists_breakdown['monthly'] as $key => $client)
                    {
                        $filteredClients[$key]['is_experimented'] = 1;
                        $filteredClients[$key]['tag'] = $this->mChimpLists[($i % ($segments/2)) + 2];
                        $filteredClientsToBeExperimented[$key] = $filteredClients[$key];
                        $i++;
                    }


                }

                // 2. send those into funnel1 (expired users list)

                // preparing members to be inserted in funnel ($filteredClients)
                $membersForFunnel = $this->prepareMembersForFunnelInsertion($filteredClients, $params);

                $retnObj = new RetentionManager();
                foreach($membersForFunnel as $member)
                {
                    //inserting in funnel
                    $retnObj->addExpiredClientInFunnel($member);
                }

                // 3. apply discount to those customers ($filteredClientsToBeExperimented)

                foreach($filteredClientsToBeExperimented as $client)
                {
                    $invoiceObj = new WHMCSInvoiceManager();
                    $invoiceObj->applyDisCountOnRenewalInvoice($client, $params);
                }

                // 4. send email to those customers

                $settings = Common::getSettings();
                $lists = $settings['mailchimp']['list_renewal_1'];

                $filteredLists = $this->prepareMemberListForMailChimp($filteredClientsToBeExperimented);

                $m = new MailChimpManager();

                foreach($filteredLists as $listName => $filteredMembers) {
                    $emailed = $m->subscribeMembersToList($lists[$listName], $filteredMembers);
                }

                if($emailed) {
                    // all 4 steps successfully completed
                    return true;
                } else {
                    $params_email = array('action_date' => date('Y-m-d H:i:s') , 'reason' => 'B');
                    Common::sendEmailIfCampaignNotRan($params_email);
                }

            }

        } catch(\Exception $e) {
            $params_email = array('action_date' => date('Y-m-d H:i:s') , 'reason' => 'C '.$e->getMessage());
            Common::sendEmailIfCampaignNotRan($params_email);
            throw new \Exception($e->getMessage());
        }


    }


    public function customerPaidInvoiceAfterDiscount($params)
    {

        try {

            //0. check if unpaid invoice in funnel In
            $retnObj = new RetentionManager();
            if($retnObj->checkIfInvoiceUnpaid($params)) {


                // 1. update funnel 1 (has_paid = true) AND move client to funnel2 (insert)
                $response = $retnObj->customerPaidInvoiceAfterDiscount($params);
                $member_email = $response['email'];
                $member_tag = $response['tag'];
                $member_amount = $response['amount'];
                $params['list_id'] = $params['lists'][$member_tag];

                //2. unsubscribe client from list

                $member = array();
                $member['email'] = $member_email;

                $mailchimpObj = new MailChimpManager();
                $response = $mailchimpObj->unsubscribeMemberFromList($member , $params['list_id']);

                //3. if (amount in WHMCS ) > (amount in bi_ret_funnel_in) send email to Furqan

                //3. a) get whmcs amount
                $params_inv = array(
                    'invoice_id' => $params['invoiceid']
                );
                $invObj = new WHMCSInvoiceManager();
                $whmcs_amount = $invObj->getTotalAmountPaidFromInvoiceId($params_inv);

                if(($whmcs_amount > $member_amount) && ($member_amount > 0))
                {
                    // send email
                    $body = '<div>
                        Hello , <br/><br/>
                        Invoice #'. $params['invoiceid'].' was discounted with amount '.$member_amount.' but rather paid amount of'. $whmcs_amount.
                        '<br/>
                        Hence, amounts mismatch. <br/><br/>
                        Thanks <br/>
                        </div>';

                    $mail = new Message();
                    $mail->setFrom('PureVPN Accounting <accounting@purevpn.com>')
                        ->setSubject('Alert for Invoice - '. $params['invoiceid'])
                        ->setHtmlBody($body);

                    $mail->addTo('furqan.khan@gaditek.com');

                    $mailer = new SendmailMailer();
                    $mailer->send($mail);
                }


                if($response) {
                    return true;
                } else {
                    throw new \Exception('Error unsubscribing client from mail list.');
                }

            } else {
                throw new \Exception('No such data');
            }

        } catch(\Exception $e) {
            throw new \Exception($e->getMessage());
        }

    }

    public function calculateCLTforClient($params)
    {

        $billing_list_data = array
        (
            'Monthly' => array('expiry_days'=>32),
            'Quarterly' => array('expiry_days'=>95),
            'Semi-Annually' => array('expiry_days'=>190),
            'Annually' => array('expiry_days'=>370),
            'One Time' => array('expiry_days'=>5)
        );

        // 1. get clients
        $clientObj = new WHMCSClientManager();
        $client = $clientObj->getClientGroupedByInvoice($params);

        // 2. check if user exists in CLT table
        $cltObj = new CLTManager();
        $userExists = $cltObj->checkIfUserExists($params);

        if($userExists) {
            // update the row (expiry_date)
            $dates_invoice_expiry = array();
            foreach($client as $invoice)
            {
                $datepaid_invoice = $invoice['datepaid'];
                $expiration_date_invoice = date("Y-m-d", strtotime('+ '.$billing_list_data[$invoice['billingcycle']]['expiry_days'].' day', strtotime($datepaid_invoice)));
                $dates_invoice_expiry[] = $expiration_date_invoice;
            }

            $clt_update = $params;
            $expiration_date = max($dates_invoice_expiry);
            $clt_update['expiration_date'] = $expiration_date;
            try
            {
                $cltObj->updateUserExpiryDate($clt_update);
            } catch(\PDOException $e)
            {
            }

        } else {
            // insert user
            $clt_insert = array(
                'p_userid' => $client[0]['userid'],
                'p_billing_cycle' => $client[0]['billingcycle'],
                'p_registration_date' => $client[0]['datecreated'],
                'p_first_invoice_date' => $client[0]['datepaid']
            );

            $dates_invoice_expiry = array();
            foreach($client as $invoice)
            {
                $datepaid_invoice = $invoice['datepaid'];
                $expiration_date_invoice = date("Y-m-d", strtotime('+ '.$billing_list_data[$invoice['billingcycle']]['expiry_days'].' day', strtotime($datepaid_invoice)));
                $dates_invoice_expiry[] = $expiration_date_invoice;
            }

            $expiration_date = max($dates_invoice_expiry);
            $clt_insert['p_expiration_date'] = $expiration_date;

            try
            {
                $cltObj->insertUser($clt_insert);
            } catch(\PDOException $e)
            {
                if($e->getCode() == 23000) {
                    // user entered already (do nothing)
                }
            }

        }

        return true;
    }

    public function calculateCLTforClientImproved($params)
    {

        //0. get ClientId from InvoiceId
        $invoiceObj = new WHMCSInvoiceManager();
        $user_id = $invoiceObj->getClientIdFromInvoiceId($params);
        $params['userid'] = $user_id;

        $billing_list_data = array
        (
            'Monthly' => array('expiry_days'=>32),
            'Quarterly' => array('expiry_days'=>95),
            'Semi-Annually' => array('expiry_days'=>190),
            'Annually' => array('expiry_days'=>370),
            'One Time' => array('expiry_days'=>5)
        );

        // 1. get clients
        $clientObj = new WHMCSClientManager();
        $client = $clientObj->getClientGroupedByInvoice($params);

        // 2. delete user from CLT table
        $cltObj = new CLTManager();
        $cltObj->removeUser($params);

        // 3. insert user
        $invoice_index = 0;
        foreach($client as $k=> $invoice)
        {
            if(isset($billing_list_data[$invoice['billing_cycle']])) {

                $clt_insert = array(
                    'p_user_id' => $invoice['user_id'],
                    'p_billing_cycle_init' => $client[0]['billing_cycle'],
                    'p_registration_date' => $invoice['registration_date'],
                    'p_invoice_id' => $invoice['invoice_id'],
                    'p_billing_cycle' => $invoice['billing_cycle'],
                    'p_total' => $invoice['total'],
                    'p_invoice_start_date' => $invoice['invoice_start_date'],
                    'p_is_first_invoice' => 0,
                    'p_affiliate_id' => $client[0]['affiliate_id'],
                );

                if($invoice_index == 0) {
                    $clt_insert['p_is_first_invoice'] = 1;
                }

                $datepaid_invoice = $invoice['invoice_start_date'];
                $expiration_date_invoice = date("Y-m-d", strtotime('+ '.$billing_list_data[$invoice['billing_cycle']]['expiry_days'].' day', strtotime($datepaid_invoice)));
                $clt_insert['p_invoice_end_date'] = $expiration_date_invoice;
                try
                {
                    $cltObj->insertInvoice($clt_insert);
                } catch(\PDOException $e)
                {
                    if($e->getCode() == 23000) {

                        // user entered already (do nothing)

                    }
                }
                $invoice_index++;
            }
        }

        return true;
    }

    public function filterListForDiscountedClients($clientList, $discount)
    {
        $filteredClients = array();
        foreach($clientList as $client)
        {
            // if client already in the filtered list
            if(!isset($filteredClients[$client['invoiceid']])) {

                // if billingcycle is among the selected plans
                if(in_array($client['billingcycle'], $discount['plans'])) {

                    //ignore corporate clients
                    $found = strpos($client['description'], "Users - Unlimited Bandwidth Package ");
                    if($found !== false) continue;

                    $found = strpos($client['description'], "Dedicated IP");
                    if($found !== false) continue;

                    // prepare client fields a/c to requirement
                    $client_filtered = $client;

                    // discount to be applied based on discount method (fixed or percentage)
                    if($discount['method'] == 'fixed') {
                        // fixed
                        $client_filtered['discount'] = $discount[$discount['method']][$client['billingcycle']];
                    } else {
                        // percentage
                        $percentage = $discount[$discount['method']][$client['billingcycle']];
                        $invoice_item_amount = $client['invoiceitemamount'];
                        $discount_val = $invoice_item_amount - (($invoice_item_amount * $percentage) / 100);
                        $client_filtered['discount'] = $discount_val;
                    }

                    $client_filtered['promo'] = ($client['invoiceitemamount'] - $client_filtered['discount']) * -1;
                    $client_filtered['due'] = $client['invoicetotal'] + $client_filtered['promo'];

                    // is_experimented = 1 ( by default)
                    $client_filtered['is_experimented'] = 1;

                    // tag = A (by default)
                    $client_filtered['tag'] = $this->mChimpFirstList;

                    // if already availed discount
                    if($discount_val >= $client['invoicetotal']) {
                        continue;
                    } else {

                        // check if valid email -- for mailchimp usage in future
                        if (!filter_var($client_filtered['email'], FILTER_VALIDATE_EMAIL)) {
                            throw new \Exception('There was an invalid email found in list. Email : '.$client_filtered['email']);
                        }

                        // if discounted value < base value, do not add client

                        if($discount_val < $discount['base_amount'][$client['billingcycle']]) {
                            continue;
                        }

                        // prepare amount for hosting (tblhosting needs amount = hosting + promohosting, excluding addon)
                        $invoice_id = $client['invoiceid'];
                        $params_ii = array('invoice_id' => $invoice_id);
                        $invoiceMgr = new WHMCSInvoiceManager();
                        $invoice_items = $invoiceMgr->getInvoiceItems($params_ii);
                        // removing Addon Items
                        $total_without_addon = 0;
                        $hosting_count = 0;
                        foreach($invoice_items as $item)
                        {
                            if($item['type'] == 'Hosting') {
                                $hosting_count++;
                            }
                            if($item['type'] != 'Addon') {
                                $total_without_addon += $item['amount'];
                            }
                        }
                        // omit if a user has multiple hostings on a single invoice
                        if($hosting_count > 1) {
                            continue;
                        }

                        // adding promo because promo has not yet been added in invoiceitems table
                        $client_filtered['total_without_addon'] = $total_without_addon;
                        $client_filtered['hosting_updated_amount'] = $total_without_addon + $client_filtered['promo'];

                        // client is filtered
                        $filteredClients[$client['invoiceid']] = $client_filtered;

                    }

                }
            }

        }
        return $filteredClients;
    }

    public function calculateCLTforAllClients($params)
    {

        $billing_list_data = array
        (
            'Monthly' => array('expiry_days'=>32),
            'Quarterly' => array('expiry_days'=>95),
            'Semi-Annually' => array('expiry_days'=>190),
            'Annually' => array('expiry_days'=>370),
            'One Time' => array('expiry_days'=>5)
        );

        // 1. get clients
        $clientObj = new WHMCSClientManager();
        $clients = $clientObj->getAllClientsGroupedByInvoice($params);

        // 2. group by user id
        $clients_grouped = array();
        foreach($clients as $client)
        {
            $clients_grouped[$client['userid']][] = $client;
        }

        $cltObj = new CLTManager();
        foreach($clients_grouped as $client)
        {

            $clt_insert = array(
                'p_userid' => $client[0]['userid'],
                'p_billing_cycle' => $client[0]['billingcycle'],
                'p_registration_date' => $client[0]['datecreated'],
                'p_first_invoice_date' => $client[0]['datepaid']
            );

            $dates_invoice_expiry = array();
            foreach($client as $invoice)
            {
                $datepaid_invoice = $invoice['datepaid'];
                $expiration_date_invoice = date("Y-m-d", strtotime('+ '.$billing_list_data[$invoice['billingcycle']]['expiry_days'].' day', strtotime($datepaid_invoice)));
                $dates_invoice_expiry[] = $expiration_date_invoice;
            }

            $expiration_date = max($dates_invoice_expiry);
            $clt_insert['p_expiration_date'] = $expiration_date;

            try
            {
                $cltObj->insertUser($clt_insert);
            } catch(\PDOException $e)
            {
                if($e->getCode() == 23000) {
                    // user entered already (do nothing)
                }
            }
        }


        //print_r($clients_grouped);
        //die();

        return true;
    }

    public function calculateCLTforAllClientsImproved($params)
    {

        $billing_list_data = array
        (
            'Monthly' => array('expiry_days'=>32),
            'Quarterly' => array('expiry_days'=>95),
            'Semi-Annually' => array('expiry_days'=>190),
            'Annually' => array('expiry_days'=>370),
            'One Time' => array('expiry_days'=>5)
        );

        // 1. get clients
        $clientObj = new WHMCSClientManager();
        $clients = $clientObj->getAllDistinctPaidHostingInvoices($params);


        // 2. group by user id
        $clients_grouped = array();
        foreach($clients as $clt)
        {
            $clients_grouped[$clt['user_id']][] = $clt;
        }

        $cltObj = new CLTManager();
        foreach($clients_grouped as $client)
        {
            $invoice_index = 0;
            foreach($client as $k=> $invoice)
            {
                if(isset($billing_list_data[$invoice['billing_cycle']])) {
                    $clt_insert = array(
                        'p_user_id' => $invoice['user_id'],
                        'p_billing_cycle_init' => $client[0]['billing_cycle'],
                        'p_registration_date' => $invoice['registration_date'],
                        'p_invoice_id' => $invoice['invoice_id'],
                        'p_billing_cycle' => $invoice['billing_cycle'],
                        'p_total' => $invoice['total'],
                        'p_invoice_start_date' => $invoice['invoice_start_date'],
                        'p_is_first_invoice' => 0,
                        'p_affiliate_id' => $client[0]['affiliate_id'],
                    );

                    if($invoice_index == 0) {
                        $clt_insert['p_is_first_invoice'] = 1;
                    }

                    $datepaid_invoice = $invoice['invoice_start_date'];
                    $expiration_date_invoice = date("Y-m-d", strtotime('+ '.$billing_list_data[$invoice['billing_cycle']]['expiry_days'].' day', strtotime($datepaid_invoice)));
                    $clt_insert['p_invoice_end_date'] = $expiration_date_invoice;
                    try
                    {
                        $cltObj->insertInvoice($clt_insert);
                    } catch(\PDOException $e)
                    {
                        if($e->getCode() == 23000) {

                            // user entered already (do nothing)

                        }
                    }
                    $invoice_index++;
                }
            }

        }
        return true;
    }

    public function historyBackup()
    {
        //1. first copy history to history backup
        $today = date('Y-m-d');
        $date_to_process = date('Y-m-d', strtotime($today . ' - 105 days'));
        $params = array('date' => $date_to_process);
        $this->db_historyBackup($params);

    }


    private function db_historyBackup($params)
    {
        $this->db = new DB('DB-SA');
        $input = array(
            'p_date' => $params['date']
        );
        // first get count of rows

        $chunk = 5000;

        $query = "SELECT COUNT(*) as count FROM history_denormalized WHERE DATE(added_on) <= :p_date";
        $result_history_count = $this->db->row($query, $input);
        $count_rows = $result_history_count['count'];

        $num_of_chunks = ceil($count_rows / $chunk);

        if($num_of_chunks > 0) {
            for($i = 0; $i < $num_of_chunks; $i++)
            {
                $chunk_number = $i * $chunk;

                $query_sub = "SELECT * FROM history_denormalized WHERE DATE(added_on) = :p_date LIMIT ".$chunk." OFFSET ".$chunk_number;
                $result_history_sub = $this->db->query($query_sub, $input);
                $count_sub_rows = $result_history_sub;
                $records = '';
                foreach($count_sub_rows as $k => $row){
                    $input_sub = array(
                        "'".$row['server']."'",
                        "'".$row['source']."'",
                        $row['pptp'],
                        $row['l2tp'],
                        $row['ikev2'],
                        $row['openvpn'],
                        $row['openvpn_tcp'],
                        $row['openvpn_udp'],
                        $row['sstp'],
                        $row['prtg'],
                        "'".$row['added_on']."'"
                    );
                    $record_str = "(".implode(",", $input_sub)." ),";
                    $records .= $record_str;

                }
                $records = substr($records, 0, -1);
                $query_sub = "INSERT INTO history_denormalized_backup ".
                    "(server, source, pptp, l2tp, ikev2, openvpn, openvpn_tcp, openvpn_udp, sstp, prtg, added_on) ".
                    "VALUES ".$records;

                $this->db->query($query_sub, array());
            }

            $query_sub_del = "DELETE FROM history_denormalized WHERE DATE(added_on) <= :p_date";
            $this->db->row($query_sub_del, $input);

            return true;

        }


    }

    private function prepareMembersForFunnelInsertion($clients , $params)
    {
        $filtered_clients = array();
        if(isset($clients)) {
            foreach($clients as $client)
            {
                $member = array(
                    'p_client_id' => $client['clientid'],
                    'p_invoice_id' => $client['invoiceid'],
                    'p_email' => $client['email'],
                    'p_has_paid' =>  0,
                    'p_billing_cycle' => $client['billingcycle'],
                    'p_campaign_type' => $params['campaign_type'],
                    'p_tag' => $client['tag'],
                    'p_amount' => $client['due'],
                    'p_is_experimented' => $client['is_experimented'],
                    'p_date_expired' => $params['date'],
                    'p_notes' => 'Tariq - Aaqib - Renewal Campaign',
                );
                $filtered_clients[] = $member;
            }
        }
        return $filtered_clients;
    }

    private function prepareMemberListForMailChimp($clients)
    {
        $filtered_list_members = array();
        if(isset($clients)) {
            foreach($clients as $client)
            {
                $member = array(
                    'email'      => array('email'=>$client['email']),
                    'merge_vars' => array(
                        'FNAME' => $client['firstname'],
                        'LNAME' => $client['lastname'],
                        'INVOICEID' => $client['invoiceid']
                    )
                );
                if(!isset($filtered_list_members[$client['tag']])) {
                    $filtered_list_members[$client['tag']] = array();
                }
                $filtered_list_members[$client['tag']][] = $member;
            }
        }
        return $filtered_list_members;
    }


}