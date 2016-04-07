<?php

namespace CAP\Coupon;

use CAP\Core\Common;
use CAP\Core\DB;
use CAP\WHMCS\Promotion\Manager AS WHMCSPromotion;

Class Manager {
    private $db;

    function __construct() {
        $this->db = new DB('DB-BI');
        $this->whmcsPromotion = new WHMCSPromotion();
    }

    public function addCoupons($request_data) {

        if($request_data['is_voucher'] == 0)
        {
            if($this->whmcsPromotion->isBatchCodeExists($request_data['batch_code'])) {
                throw new \Exception('Code already exists please choose another.');
            }
        }

        if(isset($request_data['max_values'])) {
            $coupons = $this->generateCoupons($request_data['max_values']);

            if(!empty($coupons)) {

                $this->whmcsPromotion->addCoupons($request_data, $coupons);
            }
        }
        return true;
    }

    public function editCoupon($coupon_id,$start_date,$expiry_date,$max_uses) {

        if($this->whmcsPromotion->isVoucher($coupon_id)){
            return false;
        }

        if($this->whmcsPromotion->isValidMaxUses($coupon_id,$max_uses)){
            return $this->whmcsPromotion->editCoupon($coupon_id,$start_date,$expiry_date,$max_uses);
        } else {
            return 'maxuses';
        }
    }

    public function deleteCoupon($coupon_id) {
        return $this->whmcsPromotion->deleteCoupon($coupon_id);
    }

    private function generateCoupons($max_values) {
        $chars = "23456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijkmnopqrst";
        $coupons = array();

        if($max_values > 0) {

            for($c=1; $c <= $max_values; $c++) {
                $coupon = '';
                for ($i = 0; $i < 4; $i++) {
                    $coupon .= $chars[mt_rand(0, strlen($chars)-1)];
                }
                $coupons[] = $coupon;
            }
        }
        return $coupons;
    }

    public function getPartnerCoupons($partner_id)
    {
        $coupons =  $this->whmcsPromotion->getPartnerCoupons($partner_id);
    }



    public function getPartnerCouponTypes($partner_id) {
        if(empty($partner_id)) {
            return false;
        }

        $query = "SELECT coupon_type,coupon_type_value FROM user_coupon_types AS UCT WHERE UCT.user_id = '" . $partner_id . "';";
        $results = $this->db->query($query);

        return $results;
    }

    public function getCoupon($coupon_id) {

        return $this->whmcsPromotion->getCoupon($coupon_id);
    }

    public function getCouponStats($partner_id) {
        $stats =  $this->whmcsPromotion->getCouponStats($partner_id);
        return $stats;
    }

}