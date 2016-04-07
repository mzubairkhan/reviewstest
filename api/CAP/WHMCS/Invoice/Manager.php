<?php

namespace CAP\WHMCS\Invoice;;

use CAP\Core\Common;
use CAP\Core\DB;

Class Manager {

    private $db;
    private $table;

    function __construct()
    {
        $this->db = new DB('DB-WHMCS');
        $this->table = 'tblinvoices';
    }

    public function getClientIdFromInvoiceId($params)
    {
        return $this->db_getClientIdFromInvoiceId($params);
    }

    public function getTotalAmountPaidFromInvoiceId($params)
    {
        return $this->db_getTotalAmountPaidFromInvoiceId($params);
    }

    public function applyDisCountOnRenewalInvoice($params, $extra)
    {
        return $this->db_applyDisCountOnRenewalInvoice($params, $extra);
    }

    public function getUserInvoicesOnward($params)
    {
        return $this->db_getUserInvoicesOnward($params);
    }

    public function getInvoiceItems($params)
    {
        return $this->db_getInvoiceItems($params);
    }

    public function updateHostingAmount($params)
    {
        return $this->db_updateHostingAmount($params);
    }


    private function db_updateHostingAmount($params)
    {
        // update tblhosting
        $input = array(
            'p_amount' => $params['amount'],
            'p_userid' => $params['userid']
        );
        $query = "UPDATE tblhosting SET amount = :p_amount WHERE userid = :p_userid";
        $update = $this->db->query($query, $input);
    }

    private function db_getUserInvoicesOnward($params)
    {
        $input = array(
            'p_invoice_id' => $params['invoice_id'],
            'p_client_id' => $params['client_id']
        );
        $query = "SELECT SUM(total) as total from tblinvoices i
                INNER JOIN tblinvoiceitems ii ON ii.invoiceid = i.id
                WHERE i.userid = :p_client_id AND i.id >= :p_invoice_id and status = 'Paid'
                AND ii.type = 'PromoHosting' and ii.amount < 0 ";

        $result = $this->db->row($query, $input);
        return $result['total'];

    }

    private function db_getClientIdFromInvoiceId($params)
    {
        $input = array(
            'p_invoice_id' => $params['invoice_id']
        );
        $query = "SELECT userid as client_id from tblinvoices i
                WHERE i.id = :p_invoice_id";
        $result = $this->db->row($query, $input);
        return $result['client_id'];
    }

    private function db_getTotalAmountPaidFromInvoiceId($params)
    {
        $input = array(
            'p_invoice_id' => $params['invoice_id']
        );
        $query = "SELECT total from tblinvoices i
                WHERE i.id = :p_invoice_id";
        $result = $this->db->row($query, $input);
        return $result['total'];
    }

    private function db_applyDisCountOnRenewalInvoice($params, $extra)
    {
        // update tblinvoices
        $input = array(
            'p_total' => $params['due'],
            'p_invoiceid' => $params['invoiceid']
        );
        $query = "UPDATE tblinvoices SET total = :p_total WHERE id = :p_invoiceid";
        $update = $this->db->query($query, $input);

        // insert row 'promo' in tblinvoiceitems
        $input = array(
            'p_invoiceid' => $params['invoiceid'],
            'p_userid' => $params['clientid'],
            'p_type' => $extra['type'],
            'p_relid' => $params['hostingid'],
            'p_description' => 'Renewal Discount Promotion',
            'p_amount' => $params['promo'],
            'p_duedate' => $params['duedate'],
            'p_paymentmethod' => $params['paymentmethod'],
            'p_notes' => $extra['notes'],
        );
        $query = "INSERT INTO tblinvoiceitems(invoiceid, userid, type, relid, description, amount, duedate, paymentmethod, notes)
                              VALUES(:p_invoiceid, :p_userid, :p_type, :p_relid, :p_description, :p_amount, :p_duedate, :p_paymentmethod, :p_notes)";
        $insert = $this->db->query($query,$input);

        // update tblhosting
        $input = array(
            'p_total' => $params['hosting_updated_amount'],
            'p_hostingid' => $params['hostingid']
        );
        $query = "UPDATE tblhosting SET amount = :p_total WHERE id = :p_hostingid";
        $update = $this->db->query($query, $input);

        // update tblorders
        $input = array(
            'p_total' => $params['due'],
            'p_invoiceid' => $params['invoiceid']
        );
        $query = "UPDATE tblorders SET amount = :p_total WHERE invoiceid = :p_invoiceid";
        $update = $this->db->query($query, $input);

        return true;
    }

    private function db_getInvoiceItems($params)
    {
        $input = array(
            'p_invoice_id' => $params['invoice_id']
        );
        $query = "SELECT type, amount from tblinvoiceitems ii
                WHERE ii.invoiceid = :p_invoice_id";
        $result = $this->db->query($query, $input);
        return $result;
    }

}