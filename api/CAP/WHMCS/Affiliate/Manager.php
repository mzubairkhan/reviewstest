<?php

namespace CAP\WHMCS\Affiliate;

use CAP\Core\Common;
use CAP\Core\DB;

Class Manager {

    private $db;
    private $table;

    function __construct()
    {
        $this->db = new DB('DB-WHMCS');
        $this->table = 'tblclients';
    }

    public function getAffiliatesInfo()
    {
        return $this->db_getAffiliatesInfo();
    }

    public function getAffiliateClientId($affiliate_id) {
        return $this->db_getAffiliateClientId($affiliate_id);
    }

    /*
     * Divide invoices into New/Recurring
     * */

    public function getInvoicesStatus($invoices) {
        $whmcs_invoices = array();

        $all_invoices_ids = array_map(function($invoice_id) {
            return $invoice_id;
        },array_keys($invoices)); //extract only invoice ids from $invoices array

        $new_invoices = $this->db_getNewInvoices($all_invoices_ids);

        if(!empty($new_invoices)) {

            $new_invoices_ids = array_map(function($invoice) {
                return $invoice['id'];
            },$new_invoices); //extract only invoice ids from $new_invoices array

            $new_invoices = array_fill_keys($new_invoices_ids,NULL); //create array pass invoice ids as keys and NULL for values
            $new_invoices = array_intersect_key($invoices,$new_invoices); //extract matched keys and there respective values from $invoices and $new_invoices

            $sum__totalcost = array_sum(array_map(function($invoice) {
                return $invoice['totalcost'];
            }, $new_invoices)); //extract and sum totalcost from $new_invoices

            $sum_commission = array_sum(array_map(function($invoice) {
                return $invoice['commission'];
            }, $new_invoices));  //extract and sum commission from $new_invoices

            $whmcs_invoices['new']['invoices'] = $new_invoices_ids;
            $whmcs_invoices['new']['total_sales'] = $sum__totalcost;
            $whmcs_invoices['new']['total_commission'] = $sum_commission;
        }

        $all_invoices = $this->db_getAllInvoices($all_invoices_ids);

        if(!empty($all_invoices)) {

            $all_invoices_ids = array_map(function($invoice) {
                return $invoice['id'];
            },$all_invoices); //extract only invoice ids from $all_invoices array

            $recurring_invoices_ids = array_diff($all_invoices_ids,$new_invoices_ids); //Remove new invoices ids from all to get recurring invoices ids

            if(!empty($recurring_invoices_ids)) {
                sort($recurring_invoices_ids); //sort array for proper indexing

                $recurring_invoices = array_fill_keys($recurring_invoices_ids,NULL); //create array pass invoice ids as keys and NULL for values
                $recurring_invoices = array_intersect_key($invoices,$recurring_invoices); //extract keys and there respective values from $invoices and $recurring_invoices

                $sum__totalcost = array_sum(array_map(function($invoice) {
                    return $invoice['totalcost'];
                }, $recurring_invoices)); //extract and sum totalcost from $recurring_invoices

                $sum_commission = array_sum(array_map(function($invoice) {
                    return $invoice['commission'];
                }, $recurring_invoices)); //extract and sum commission from $recurring_invoices

                $whmcs_invoices['recurring']['invoices'] = array_keys($recurring_invoices);
                $whmcs_invoices['recurring']['total_sales'] = $sum__totalcost;
                $whmcs_invoices['recurring']['total_commission'] = $sum_commission;
            }
        }

        return $whmcs_invoices;
    }

    public function getInvoices($invoices) {
        return $this->db_getInvoices($invoices);
    }


    public function getInvoiceDetails($invoice_id) {
        return $this->db_getInvoiceDetails($invoice_id);
    }

    private function db_getAffiliatesInfo()
    {
        $query = "SELECT C.firstname as first_name, C.lastname as last_name, C.email as email, P.id as affiliate_id
                    FROM tblclients C
                    INNER JOIN tblaffiliates_pap P ON C.id = P.clientid";
        $result = $this->db->query($query);
        return $result;
    }

    private function db_getAffiliateClientId($affiliate_id) {
        $query = "SELECT AP.clientid FROM tblaffiliates_pap AS AP WHERE AP.id = :affiliate_id;";

        return $this->db->query($query, array('affiliate_id' => $affiliate_id));
    }

    private function db_getNewInvoices($invoices) {
        $query = "SELECT I.id FROM tblinvoices AS I
                    INNER JOIN tblorders AS O ON (O.invoiceid = I.id)
                    WHERE 1=1
                    AND I.id IN (" . implode(',',$invoices) . ");";

        return $this->db->query($query);
    }

    private function db_getAllInvoices($invoices) {
        $query = "SELECT I.id FROM tblinvoices AS I
                    WHERE I.id IN (" . implode(',',$invoices) . ");";

        return $this->db->query($query);
    }

    private function db_getInvoices($invoices) {
        if(!empty($invoices)) {
            $query = "SELECT I.*,H.billingcycle,II.type,II.relid FROM tblinvoices AS I
                        INNER JOIN tblinvoiceitems AS II ON (II.invoiceid=I.id AND II.type='Hosting')
                        INNER JOIN tblhosting AS H ON (H.id=II.relid)
                        WHERE 1=1 AND I.id IN($invoices) GROUP BY I.id;";

            return $this->db->query($query);
        }
        return false;
    }

    private function db_getInvoiceDetails($invoice_id) {
        if(!empty($invoice_id)) {
            $query = "SELECT C.email AS client_email,
I.id AS invoice_id, I.date AS invoice_date, I.duedate AS invoice_duedate, I.datepaid AS invoice_datepaid, I.subtotal AS invoice_subtotal, I.total AS invoice_total, I.status AS invoice_status, I.paymentmethod AS invoice_paymentmethod,
II.id AS item_id,II.type AS item_type,II.description AS item_description,II.amount AS item_amount,II.id AS item_id,II.duedate AS item_duedate,II.paymentmethod AS item_paymentmethod,H.billingcycle AS hosting_billing_cycle
FROM tblinvoices AS I
INNER JOIN tblinvoiceitems AS II ON (II.invoiceid=I.id)
INNER JOIN tblhosting AS H ON (II.relid=H.id)
INNER JOIN tblclients AS C ON (I.userid=C.id)
WHERE 1=1 AND I.id=:invoice_id;";

            $inputs = array(
                'invoice_id' => $invoice_id,
            );

            return $this->db->query($query,$inputs);
        }
        return false;
    }

}