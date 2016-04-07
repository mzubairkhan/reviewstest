<?php

namespace CAP\WHMCS\Promotion;

use CAP\Core\Common;
use CAP\Core\DB;
use SSP;

Class Manager {
    private $db;

    function __construct() {
        $this->db = new DB('DB-WHMCS');
    }

    public function addCoupons($coupon_data, $coupon_codes) {

        if(empty($coupon_data) || empty($coupon_codes)) {
            return false;
        }

        foreach($coupon_data as $field => $value) {
            ${$field} = $value;
        }

        $coupon_ids = array();
        foreach($coupon_codes as $coupon_code) {

            $promotion_values = array();

            if($is_voucher) {
                $promotion_values['code'] = $batch_code . $coupon_code;
                $promotion_values['maxuses'] = 1;
            } else {
                $promotion_values['code'] = $batch_code;
                $promotion_values['maxuses'] = $max_uses;
            }

            $promotion_values['code'] = $promotion_values['code'];
            $promotion_values['type'] = $coupon_type;
            $promotion_values['value'] = $coupon_value;
            $promotion_values['startdate'] = $start_date;
            $promotion_values['expirationdate'] = $expiry_date;
            $promotion_values['notes'] = $notes;

            /* hard coded values */
            $promotion_values['cycles'] = "Monthly,Quarterly,Semi-Annually,Annually";
            $promotion_values['appliesto'] = "161,154,41,160";
            $promotion_values['recurring'] = 1;
            $promotion_values['applyonce'] = 1;

            $values = array();
            foreach($promotion_values as $k =>  $value)
            {
                //   $promotion_values[$k] = $this->mysql_pre($value);
                $values[$k] = $value;
            }

            //Common::dumpAndDie($promotion_values);
            $promotion_query = "INSERT INTO tblpromotions (" . implode(',',array_keys($promotion_values)) . ") VALUES (:" . implode(',:',array_keys($values)) . ")";
//            Common::dump($values);
//            Common::dumpAndDie($promotion_query);

            $result = $this->db->query($promotion_query, $values);

            if($result) {
                $coupon_ids[] = $this->db->lastInsertId();
            }
        }

        $this->bindPartnerCoupons($coupon_ids, $partner_id, $is_voucher, $batch_code);

    }

    public function bindPartnerCoupons($coupon_ids, $partner_id, $is_voucher, $batch_code) {

        foreach($coupon_ids as $coupon_id) {
            $partner_coupon_values = array();
            $partner_coupon_values['partner_id'] = $partner_id;
            $partner_coupon_values['coupon_id'] = $coupon_id;
            $partner_coupon_values['is_voucher'] = $is_voucher;
            $partner_coupon_values['batch_code'] = "'" . $batch_code . "'";
            $partner_coupon_values['created'] = "'" . date('Y-m-d H:i:s') . "'";

            $partner_coupon_query_values[] = "(" . implode(',', array_values($partner_coupon_values)) . ")";

        }

        $partner_coupon_query = "INSERT INTO partner_coupons (`partner_id`,`coupon_id`,`is_voucher`,`batch_code`,`created`) VALUES " . implode(',',$partner_coupon_query_values) . ";";

        return $this->db->query($partner_coupon_query);
    }

    public function isVoucher($coupon_id) {
        $query = "SELECT C.is_voucher FROM partner_coupons AS C WHERE C.coupon_id='" . $coupon_id . "'";
        $result = $this->db->query($query);
        if(!empty($result)) {

            return $result[0]['is_voucher'];
        }
        return false;
    }

    public function isValidMaxUses($coupon_id,$max_uses) {

        $query = "SELECT P.uses FROM tblpromotions AS P WHERE P.id='" . $coupon_id . "'";
        $result = $this->db->query($query);
        if(!empty($result)) {
            return ($result[0]['uses'] < $max_uses);
        }
        return false;
    }

    public function isBatchCodeExists($batch_code) {

        $query = "SELECT COUNT(P.id) as count FROM partner_coupons AS P WHERE P.batch_code='" . $batch_code . "'";
        $result = $this->db->row($query);
        return $result['count'];

    }

    public function editCoupon($coupon_id,$start_date,$expiry_date,$max_uses) {
        $query_params = array();

        if(!empty($start_date)) {
            $query_params[] = "P.startdate='" . $start_date . "'";
        }
        if(!empty($expiry_date)) {
            $query_params[] = "P.expirationdate='" . $expiry_date . "'";
        }
        if(!empty($max_uses)) {
            $query_params[] = "P.maxuses='" . $max_uses . "'";
        }

        if(!empty($query_params)) {
            $query = "UPDATE tblpromotions AS P SET " . implode(',',$query_params) . " WHERE P.id='" . $coupon_id . "'";
            $result = $this->db->query($query);
            return array('id' => $coupon_id);

        }
        return false;
    }

    public function deleteCoupon($coupon_id) {
        $query = "DELETE FROM tblpromotions WHERE id='" . $coupon_id . "'";

        if($this->db->query($query)) {
            $query2 = "DELETE FROM partner_coupons WHERE coupon_id='" . $coupon_id . "'";
            return $this->db->query($query2);
        }
    }

    public function getPartnerCoupons($partner_id) {

        if(empty($partner_id)) {
            return false;
        }

        // DB table to use
        $table = 'tblpromotions';

        // Table's primary key
        $primaryKey = 'id';

        $columns = array(
            array(
                'db' => 'P.id as id',
                'dt' => 'DT_RowId',
                'formatter' => function( $d, $row ) {
                    return $d;
                }
            ),
                array( 'db' => 'P.code', 'dt' => 0, 'field' => 'code'),
            array( 'db' => 'PC.is_voucher',  'dt' => 1, 'field' => 'is_voucher' ),
            array( 'db' => 'P.type',   'dt' => 2, 'field' => 'type' ),
            array( 'db' => 'P.value','dt' => 3, 'field' => 'value'),
            array( 'db' => 'P.maxuses','dt' => 4, 'field' => 'maxuses'),
            array( 'db' => 'P.uses','dt' => 5, 'field' => 'uses'),
            array( 'db' => 'P.startdate','dt' => 6, 'field' => 'startdate'),
            array( 'db' => 'P.expirationdate','dt' => 7, 'field' => 'expirationdate')
        );

        $joinQuery = "FROM tblpromotions P INNER JOIN partner_coupons PC ON PC.coupon_id = P.id";
        $extraWhere = " PC.partner_id= $partner_id";//"`u`.`salary` >= 90000";

        $settings = $this->db->getSettings();
        $this->sql_details = array(
            'user' => $settings['user'],
            'pass' => $settings['password'],
            'db'   => $settings['dbname'],
            'host' => $settings['host']
        );

        echo json_encode(
            SSP::simple( $_GET, $this->sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere )
        );
    }

    public function getCoupon($coupon_id) {
        $query = "SELECT * FROM tblpromotions WHERE id='" . $coupon_id . "'";
        return $this->db->row($query);
    }

    public function getCouponStats($partner_id) {
        $query = "SELECT COUNT(P.id) AS total_count,(SUM(P.maxuses)-SUM(P.uses)) AS total_remaining,SUM(P.uses) AS total_used, PC.is_voucher
                    FROM partner_coupons AS PC
                    INNER JOIN tblpromotions AS P ON (P.id=PC.coupon_id)
                    WHERE PC.partner_id='" . $partner_id . "' GROUP BY PC.is_voucher;";
        return $this->db->query($query);
    }

    public function getBuyerDetails($params)
    {
        $input = array(
            'p_code' => "%".$params['code']."%"
        );
        $query = "SELECT C.id as ClientId, C.firstname, C.lastname, C.email FROM tblinvoiceitems II INNER JOIN tblclients C ON C.id = II.userid WHERE type='PromoHosting'  AND description LIKE :p_code ";
        $result = $this->db->row($query, $input);
        return $result;
    }

    public function getBuyerNRICDetails($params)
    {
        $input = array(
            'p_ClientId' => $params['ClientId']
        );
        $query = "SELECT value as valueNRIC from tblcustomfieldsvalues WHERE fieldid=19 AND relid = :p_ClientId";
        $result = $this->db->row($query, $input);
        return $result['valueNRIC'];
    }

    public function getBuyerVPNDetails($params)
    {
        $input = array(
            'p_ClientId' => $params['ClientId']
        );
        $query = "SELECT value as valueUserName from tblcustomfieldsvalues WHERE fieldid=10 AND relid = :p_ClientId";
        $result = $this->db->row($query, $input);
        return $result['valueUserName'];
    }

    public function getIfBuyerPaidAnyInvoice($params)
    {
        $input = array(
            'p_ClientId' => $params['ClientId']
        );
        $query = "SELECT count(*) as countPaid from tblinvoices WHERE userid = :p_ClientId";
        $result = $this->db->row($query, $input);
        if($result['countPaid'] > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getIfBuyerPaidPromoInvoice($params)
    {
        $input = array(
            'p_ClientId' => $params['ClientId'],
            'p_code' => $params['code'],
        );
        $query = "SELECT invoiceid from tblorders WHERE userid = :p_ClientId AND promocode = :p_code";
        $result = $this->db->row($query, $input);
        if(isset($result['invoiceid'])) {
            $input = array(
                'p_invoiceid' => $result['invoiceid']
            );
            $query = "SELECT count(*) as countPaid from tblinvoices WHERE id = :p_invoiceid AND status='Paid'";
            $result = $this->db->row($query, $input);
            if($result['countPaid'] > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


    function mysql_pre($value) {

        $magic_quotes_active = get_magic_quotes_gpc();
        $new_enough_php = function_exists("mysql_real_escape_string(unescaped_string)"); //i.e. PHP >= v4.3.0

        if($new_enough_php) { //PHP v4.3.0 or higher
            //undo any magic quote effect so mysql_real_escape_string can do the work

            if($magic_quotes_active) {$value = stripslashes($value);}

            $value = mysql_real_escape_string($value);

        } else { //before PHP v4.3.0

            if(!$magic_quotes_active) {
                $value = addslashes($value);
            }
        }
        return $value;
    }

}