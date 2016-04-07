<?php

namespace CAP\MailchimpAPI;

use CAP\Core\Common;
use CAP\Core\DB;
use Mailchimp;

Class Manager {

    private $mChimp;

    function __construct()
    {
        $settings = Common::getSettings();
        $apikey = $settings['mailchimp']['apikey'];
        $this->mChimp = new Mailchimp($apikey);
    }

    public function getList()
    {
        //$list = $this->mChimp->call('lists/list',''); print_r($list);

    }

    public function subscribeMembersToList($listid, $members)
    {
        $listOfMembersToSubscribe = array(
            'id'                => $listid,
            'batch'             => $members,
            'double_optin'      => false,
            'update_existing'   => true,
            'replace_interests' => false,
            'send_welcome'      => false
        );

        $response = $this->mChimp->call('lists/batch-subscribe', $listOfMembersToSubscribe);

        if(isset($response) && $response['error_count'] == 0) {
            return true;
        } else {
            return false;
        }
    }

    public function subscribeMemberToList($member,$listid)
    {
        $memberToSubscribe = array(
            'id'                => $listid,
            'email'             => $member['email'],
            'merge_vars'        => $member['merge_vars'],
            'double_optin'      => false,
            'update_existing'   => true,
            'replace_interests' => false,
            'send_welcome'      => false,
        );

        $response = $this->mChimp->call('lists/subscribe', $memberToSubscribe);

        if(isset($response) && !empty($response['email'])) {
            return true;
        } else {
            return false;
        }
    }

    public function unsubscribeMemberFromList($member , $listid)
    {
        $memberToUnsubscribe = array(
            'id'                => $listid,
            'email'             => $member,
            'delete_member'     => true,
            'send_goodbye'      => false,
            'send_notify'       => false
        );
        $response = $this->mChimp->call('lists/unsubscribe', $memberToUnsubscribe);
        if(isset($response) && $response['complete'] == true) {
            return true;
        } else {
            return false;
        }

    }

}