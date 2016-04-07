<?php

namespace CAP\Core;

use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;

class Common {

    public static $production_mode = 0;

    public static function getSettings()
    {

        $settings_file = "settings_local.ini";

        if($_SERVER['HTTP_HOST'] == 'phpstack-8071-17913-41538.cloudwaysapps.com') {
            $settings_file = "settings_staging.ini";
        }
        $db_settings = parse_ini_file($settings_file, true);
        return $db_settings;
    }

    public static function getCouponTypes()
    {
        $arr = array(
            'percentage' => 0,
            'fix' => 1
        );
        return $arr;
    }

    public static function getDiscountValuesForRenewal()
    {
        $discount = array(
            'plans' => array(
                'Monthly', 'Quarterly', 'Semi-Annually', 'Annually'
            ),
            'fixed' => array(
                'Monthly' => 3,
                'Quarterly' => 18.71,
                'Semi-Annually' => 33.71,
                'Annually' => 39.99
            ),
            'percentage' => array(
                'Monthly' => 10,
                'Quarterly' => 10,
                'Semi-Annually' => 10,
                'Annually' => 10
            ),
            'base_amount' => array(
                'Monthly' => 5.95,
                'Quarterly' => 18,
                'Semi-Annually' => 36,
                'Annually' => 44
            ),
            'method' => 'percentage'
        );
        return $discount;
    }

    public static function dump($data)
    {
        echo '<pre>';
        print_r($data);
    }

    public static function dumpAndDie($data)
    {
        echo '<pre>';
        print_r($data);
        die();
    }

    public static function sendEmailIfCampaignNotRan($params)
    {
        $body = '<div>
                        Hello , <br/><br/>
                        Campaign did not ran for action date '.$params['action_date'].' due to '.$params['reason'].
            '<br/> <br/>
            Please take prompt action. <br/><br/>
            Thanks <br/>
            </div>';

        $mail = new Message();
        $mail->setFrom('PureVPN Retention <retention@purevpn.com>')
            ->setSubject('Renewal Campaign Not Ran')
            ->setHtmlBody($body);

        $mail->addTo('tariq.mehmood@gaditek.com');

        $mailer = new SendmailMailer();
        $mailer->send($mail);
    }

    public static function getIPAddress()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
        {
            $ip=$_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
        {
            $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
            $ip=$_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public static function dump_die($input = array(), $exit = true) {
        echo "<pre>";
        print_r($input);
        echo "</pre>";

        if($exit) exit;


    }

    public static function file_upload_handler($file, $field_name, $slug) {

        $setting = self::getSettings();
        $_FILES = $file;
        $upload_path = $setting['upload_path']['path'];

        $temporary = explode(".", $_FILES[$field_name]["name"]);
        $file_extension = end($temporary);
        $file_name = $slug.'_'.uniqid().'.'.$file_extension;

        $sourcePath = $_FILES[$field_name]['tmp_name'];
        $targetPath = $upload_path . $file_name;
        move_uploaded_file($sourcePath, $targetPath);

        $output = array();
        $output['file_path'] = $targetPath;
        $output['file_name'] = $file_name;
        return $output;
    }

    public static function file_unlink($file){
        $upload_path = self::files_path();
        if(unlink($upload_path. $file)) {
            return true;
        }

        return false;
    }

    public static function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

        // trim
        $text = trim($text, '-');

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // lowercase
        $text = strtolower($text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        if (empty($text))
        {
            return 'n-a';
        }

        return $text;
    }


    public static function curl_res($target){
        $ch = curl_init();

        // set url
        curl_setopt($ch, CURLOPT_URL, $target);

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string
        $output = curl_exec($ch);

        // close curl resource to free up system resources
        curl_close($ch);
        return $output;

    }


    private static function get_headers_from_curl_response($response)
    {
        $headers = array();

        $header_text = substr($response, 0, strpos($response, "\r\n\r\n"));

        foreach (explode("\r\n", $header_text) as $i => $line)
            if ($i === 0)
                $headers['http_code'] = $line;
            else
            {
                list ($key, $value) = explode(': ', $line);

                $headers[$key] = $value;
            }

        return $headers;
    }

}

?>