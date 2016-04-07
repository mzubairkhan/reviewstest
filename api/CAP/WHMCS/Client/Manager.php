<?php

namespace CAP\WHMCS\Client;;

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

    public function getClientsWhoHaveNotRenewed($params)
    {
        return $this->db_getClientsWhoHaveNotRenewed($params);
    }

    public function getClientsWhoHaveNotPaid($params)
    {
        return $this->db_getClientsWhoHaveNotPaid($params);
    }


    public function filterResultSetByParams($resultSet, $params)
    {
        $filtered = array();
        foreach($resultSet as $row)
        {
            $filtered_row = array();
            foreach($params as $param) {
                $filtered_row[$param] = $row[$param];
            }
            $filtered[] = $filtered_row;
        }
        return $filtered;
    }

    public function getClientGroupedByInvoice($params)
    {
        return $this->db_getClientGroupedByInvoice($params);
    }

    private function db_getClientGroupedByInvoice($params)
    {
        $input = array(
            'p_userid' => $params['userid']
        );
        $query = "SELECT DISTINCT(I.id) invoice_id, C.id user_id, C.datecreated registration_date, H.billingcycle billing_cycle, DATE(I.datepaid) invoice_start_date, I.total AS total , CT.affiliateid as affiliate_id
					FROM tblinvoices AS I
					INNER JOIN tblinvoiceitems AS II ON II.invoiceid = I.id
					INNER JOIN tblclients AS C ON C.id = I.userid
					INNER JOIN tblhosting AS H ON H.id = II.relid
                    LEFT JOIN tblaffiliate_customer_tracking AS CT ON C.id = CT.clientid
					WHERE
					C.id = :p_userid
					AND I.status = 'Paid'
					AND II.type = 'Hosting'
					ORDER BY C.id, I.id ASC";

        $result = $this->db->query($query, $input);
        return $result;
    }


    public function getAllDistinctPaidHostingInvoices($params)
    {
        return $this->db_getAllDistinctPaidHostingInvoices($params);
    }

    private function db_getAllDistinctPaidHostingInvoices($params)
    {
        $input = array(
            'p_start_user_id' => $params['start_user_id'],
            'p_end_user_id' => $params['end_user_id']
        );
        $query = "SELECT DISTINCT(I.id) invoice_id, C.id user_id, C.datecreated registration_date, H.billingcycle billing_cycle, DATE(I.datepaid) invoice_start_date, I.total AS total , CT.affiliateid as affiliate_id
					FROM tblinvoices AS I
					INNER JOIN tblinvoiceitems AS II ON II.invoiceid = I.id
					INNER JOIN tblclients AS C ON C.id = I.userid
					INNER JOIN tblhosting AS H ON H.id = II.relid
					LEFT JOIN tblaffiliate_customer_tracking AS CT ON C.id = CT.clientid
					WHERE
					C.id >= :p_start_user_id && C.id <= :p_end_user_id
					AND I.status = 'Paid'
					AND II.type = 'Hosting'
					ORDER BY C.id, I.id ASC";

        $result = $this->db->query($query, $input);
        return $result;
    }

    public function getAllClientsGroupedByInvoice($params)
    {
        return $this->db_getAllClientsGroupedByInvoice($params);
    }

    private function db_getAllClientsGroupedByInvoice($params)
    {
        $input = array(
            'p_start_date' => $params['start_date'],
            'p_end_date' => $params['end_date']
        );
        $query = "SELECT I.id invoice_id, C.id userid, C.datecreated datecreated, H.billingcycle billingcycle, I.datepaid datepaid, I.total AS amount
					FROM tblinvoices AS I
					INNER JOIN tblinvoiceitems AS II ON II.invoiceid = I.id
					INNER JOIN tblclients AS C ON C.id = I.userid
					INNER JOIN tblhosting AS H ON H.id = II.relid
					WHERE
					SUBSTR(C.datecreated,1,10)>=:p_start_date AND SUBSTR(C.datecreated,1,10)<= :p_end_date
					AND I.status = 'Paid'
					AND II.type = 'Hosting'
					ORDER BY C.id, I.id ASC";

        $result = $this->db->query($query, $input);
        return $result;
    }

    private function db_getClientsWhoHaveNotRenewed($params)
    {
        $input = array(
            'p_input_date' => $params['date']
        );
        $query = "SELECT c.id clientid, c.firstname firstname, c.lastname lastname, c.email email, i.id invoiceid, i.userid, i.date AS invoicedate, i.duedate AS duedate, i.datepaid AS date_paid, h.nextduedate as expirydate , DATEDIFF( i.duedate, NOW() ) AS dd,
                i.paymentmethod paymentmethod, i.total invoicetotal, h.id hostingid, ii.amount invoiceitemamount, ii.id itemid, h.amount hostingamount, h.billingcycle billingcycle, ii.description description
                from tblinvoiceitems ii
                inner join tblinvoices i on i.id = ii.invoiceid
                inner join tblhosting h on h.id = ii.relid
                inner join tblclients c on c.id = i.userid
                WHERE i.status = 'Unpaid' AND DATEDIFF( i.duedate, i.date ) >=7 AND DATE(i.duedate) = :p_input_date
                and ii.type = 'Hosting'";

        $result = $this->db->query($query, $input);
        return $result;
    }

    private function db_getClientsWhoHaveNotPaid($params)
    {
        $input = array(
            'p_input_date' => $params['date']
        );
        $query = "SELECT c.id clientid, c.firstname firstname, c.lastname lastname, c.email email, h.billingcycle billing_cycle, i.status, i.id invoiceid, i.userid, i.date AS invoicedate, i.duedate AS duedate
                from tblinvoiceitems ii
                inner join tblinvoices i on i.id = ii.invoiceid
                inner join tblhosting h on h.id = ii.relid
                inner join tblclients c on c.id = i.userid
                WHERE c.datecreated = :p_input_date
                and ii.type = 'Hosting'";

        $result = $this->db->query($query, $input);
        return $result;
    }


}