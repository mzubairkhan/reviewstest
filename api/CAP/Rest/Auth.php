<?php

namespace CAP\Rest;


use \Luracast\Restler\iAuthenticate;
use \Luracast\Restler\Resources;

use CAP\Core\Common;
use CAP\User\Manager as UserManager;
use CAP\Acl\Manager as AclManager;

class Auth implements iAuthenticate
{
    /**
     * For abstract method implementation __getWWWAuthenticateString()
     * Because of following:
     * Updating dependencies (including require-dev)
     *  - Updating luracast/restler 3.x-dev (2469c7a => 8c01070)
     *    Checking out 8c010705d2825e330ad2bb5211e41dfaf85d17df
     *
     */
    public $setWWWAuthenticateString = 'Digest';

    /**
     * Responsible for implementing authentication on declared services
     *
     * __isAllowed is the implementation of the iAuthenticate interface,
     * Hence two __ (underscores) as a prefix, only PHP magic functions should have that.
     * @return bool
     * @throws \Exception
     * @codingStandardsIgnoreStart
     */
    public function __isAllowed()
    {

        $token = '';

        try{

            if ( !empty($_REQUEST['token'])
                && !empty($_REQUEST['u_id'])
            ) {

                $userObj = new UserManager();
                $userId = $_REQUEST['u_id'];
                $token = $userObj->getTokenByUserId($userId);

                // admin special check
                $params = array(
                    'user_id' => $userId
                );
                if($userObj->isAdmin($params)) {
                    return true;
                }


            } else {

                throw new \Exception('Unsupported Request: missing critical parameter(s).');

            }

        } catch (\Exception $ex){

            Api::deniedResponse(
                $ex->getMessage(), //"Invalid signature",
                403,
                Constants::STATUS_DENIED,
                $ex,
                true
            );

        }

        $found = false;
        if ( $token === $_REQUEST['token']) {

            //1. checking further for ACL

            //a. get user Permissions
            $aclObj = new AclManager();
            $params = array(
                'user_id' => $_REQUEST['u_id'],
                'is_backend' => 1
            );
            $permissions = $aclObj->getUserPermissions($params);

            //b. get called API method
            $methodName = $this->restler->apiMethodInfo->methodName;
            $className = $this->restler->apiMethodInfo->className;

            $resource = $className.'-'.$methodName;

            //c. check if method valid
            foreach($permissions as $permission)
            {
                if($permission['title'] == $resource) {
                    $found = true;
                }
            }

        }

        if($found) {
            // exceptional condition for VpnNetworkApiUser Class
            if($className == 'CAP\Rest\VpnNetworkApiUser') {

                // check in IP database
                $ipUser = Common::getIPAddress();
                $params_verify_user = array(
                    'user_id' => $userId,
                    'ip' => $ipUser
                );
                if($userObj->checkIP($params_verify_user)) {
                    return true;
                }
            } else {
                return true;
            }

        } else {
            return false;
        }

    }



    /**
     * Verification of timestamp provided by client-side
     *
     * @param $timeStamp
     * @return bool
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    protected function timeCheck($timeStamp)
    {
        if ( empty($timeStamp) ) {
            throw new \InvalidArgumentException("Empty timestamp.");
        }

        $date = new \DateTime(Constants::DATETIME_UTC);
        $givenTimeStamp = $date->setTimestamp($timeStamp);

        $dateTimeNow = new \DateTime(Constants::DATETIME_UTC);
        $interval = $dateTimeNow->diff($givenTimeStamp);

        $minutes = $interval->days * 24 * 60;
        $minutes += $interval->h * 60;
        $minutes += $interval->i;

        if ( Constants::SERVICE_TIMESTAMP_LIMIT >= $minutes ) {
            return true;
        } else {
            throw new \Exception("Time limit exceeded by {$minutes} minutes.");
        }
    }

    /**
     * Verification of hash provided by client-side
     *
     * @param $signature
     * @param $apiKey
     * @param $requestData
     * @return bool
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    protected function hashCheck($signature, $apiKey, $requestData)
    {
        if ( empty($signature) || 0 === count(trim($signature)) ) {
            throw new \InvalidArgumentException("Invalid signature.");
        }

        if ( empty($apiKey) ) {
            throw new \InvalidArgumentException("Invalid apiKey.");
        }

        $generatedHash = $this->_generateHash(
            $this->getSecretKeyByPublicKey($apiKey),
            $requestData,
            Constants::ENCRYPTION_METHOD
        );

        if ( $signature === $generatedHash) {
            return true;
        }

        Api::restLog("Given: ".$signature);
        Api::restLog("Generated: ".$generatedHash);
        Api::restLog(print_r($requestData, true));

        throw new \Exception("Bad signature.");
    }

    /**
     * Responsible for generating Hash from give array/input
     *
     * @param $secretKey
     * @param $inputArray
     * @param string $encryptMethod
     * @return string
     * @throws \InvalidArgumentException
     */
    private function _generateHash(
        $secretKey,
        $inputArray,
        $encryptMethod=Constants::ENCRYPTION_METHOD
    ) {

        if ( empty($secretKey) || 0 === count(trim($secretKey)) ) {
            throw new \InvalidArgumentException("Key not found.");
        }

        if ( empty($inputArray) ) {
            throw new \InvalidArgumentException("Invalid Request data.");
        }

        $ampersand  = "";
        $hashFeed   = "";

        foreach ($inputArray as $key=>$value) {

            if ( $key === Constants::STR_SIGNATURE ) {
                continue;
            }

            $hashFeed .= $ampersand . $key . "=" .$value;
            $ampersand = "&";

        }

        //Api::restLog($hashFeed);
        return base64_encode(hash_hmac($encryptMethod, $hashFeed, $secretKey));
    }

    /**
     * Retrieve SecretKey using PublicKey
     *
     * @param $apiKey
     * @return mixed
     * @throws \Exception
     */
    protected function getSecretKeyByPublicKey($apiKey)
    {
        if ( empty($apiKey) ) {
            throw new \Exception('Empty apiKey');
        }
        try {
            return \AccountManager::getSecretKeyByPublicKey($apiKey);
        } catch (\Exception $e) {
            Api::deniedResponse(
                'SecretKey by PublicKey not found.',
                403,
                Constants::STATUS_DENIED,
                $e,
                true
            );
        }
    }

    /**
     * Verify if date is of the format MM/YYYY
     *
     * @param $date
     * @return bool
     */
    public static function verifyDate($date)
    {
        if ( self::checkDateFormat($date) ) {

            //verify if date is greater than today
            try{

                $dateGiven = \DateTime::createFromFormat('m/Y', $date);
                $dateToday = \DateTime::createFromFormat('m/Y', date('m/Y'));

                if ($dateGiven > $dateToday) {
                    return true;
                }
                Api::restLog(
                    "dateGiven [{$dateGiven->format('m/Y')}] > dateToday [{$dateToday->format('m/Y')}] :: FAILED!"
                );

            } catch (\Exception $e) {

                Api::invalidResponse(
                    "Invalid expiryDate",
                    400,
                    Constants::STATUS_INVALID,
                    $e,
                    true
                );
            }

        }

        Api::invalidResponse(
            "Invalid expiryDate :: checkDateFormat failed",
            400,
            Constants::STATUS_INVALID,
            "",
            true
        );
        return false;
    }

    /**
     * Check if the date format is correct
     *
     * @param $date
     * @return bool
     */
    public static function checkDateFormat($date)
    {
        try{

            if ( !strstr($date, '/') ) {

                Api::restLog( "checkDateFormat [ {$date} ]" );
                Api::restLog( "expecting [ {$date} ] of format MM/YYYY" );

            } else {

                $dateArray = explode('/', $date);

                // forming proper date for verification
                // assuming card expiry as first day of month
                // adding DAY in expiry as 01
                $formedDate = "01-" . $dateArray[0] . "-" . $dateArray[1];

                $checkedDate = new \DateTime($formedDate);
                $formedCheckedDate = $checkedDate->format('m/Y');

                if ( $formedCheckedDate === $date) {
                    return true;
                }
                Api::restLog(__METHOD__ . "::Given:[{$date}] applied [{$formedDate}]" );
                Api::restLog( "checkDateFormat({$date}) is NOT {$formedCheckedDate}" );
            }

            Api::invalidResponse(
                "Invalid expiryDate :: malformed Date",
                400,
                Constants::STATUS_INVALID,
                "",
                true
            );

        } catch (\Exception $e){
            Api::invalidResponse(
                "Invalid expiryDate :: not a valid Date",
                400,
                Constants::STATUS_INVALID,
                $e,
                true
            );
        }
        return false;
    }

    /**
     * Check credit-card type, among VIPConnect supported credit-cards
     * Wrapper method for CardType::getSupportedCardTypeList()
     * PHP : in_array is case-sensitive
     * Hence e.g. $givenCard = 'VISA' will be FALSE for 'Visa' in CardType::getSupportedCardTypeList()
     *
     * @param $givenCard
     * @return bool
     */
    public static function checkCreditCardType($givenCard)
    {
        if ( in_array($givenCard, \CardType::getSupportedCardTypeList())) {
            return true;
        }
        return false;
    }

    /**
     * Wrapper method for CardType::isValidCard
     * givenCardNumber must be numeric and greater than ZERO
     *
     * @param $givenCardNumber
     * @return bool
     */
    public static function checkCreditCardNumber($givenCardNumber)
    {
        if (!is_numeric($givenCardNumber) || $givenCardNumber <= 0 ) {
            return false;
        }

        return \CardType::isValidCard($givenCardNumber);
    }

    /**
     * This method will ensure that security code is of 2-4 chars ONLY
     * Cannot perform ZERO check, as combination of ZERO is acceptable i.e. 0000
     *
     * @param $ccSecurityCode
     * @return bool
     */
    public static function checkCardSecurityCode($ccSecurityCode)
    {
        if (!is_numeric($ccSecurityCode)) {
            return false;
        }

        $ccSecurityCode = (String) $ccSecurityCode;
        if (strlen($ccSecurityCode) >= 2 && strlen($ccSecurityCode) <= 4) {
            return true;
        }

        return false;
    }

    /**
     * This method will ensure that card-contact-status is between 0-7 ONLY
     * Float point values are not acceptable
     * ZERO is acceptable, see Status::getAllStatus
     *
     * @param $givenStatus
     * @return bool
     */
    public static function checkCardContactStatus($givenStatus)
    {
        if (!is_numeric($givenStatus)) {
            return false;
        }

        if ( in_array($givenStatus, \Status::getAllStatus())) {
            return true;
        }

        return false;
    }

    /**
     * Verify given accountNumber
     * accountNumber must be numeric and greater than ZERO
     *
     * @param $accountNumber
     * @return bool
     */
    public static function checkAccountNumber($accountNumber)
    {
        if ( !is_numeric($accountNumber) || $accountNumber <= 0 ) {
            return false;
        }

        try{
            $resultSet = \AccountDao::getAccount($accountNumber);
            if ( !empty($resultSet) ) {
                return true;
            }
        } catch (\Exception $e) {
            Api::restLogException($e);
        }

        return false;
    }

    /**
     * Verify credit-card-id by sp_cc_by_id
     * ccId must be numeric and greater than ZERO
     *
     * @param $ccId
     * @return bool
     */
    public static function checkCreditCardId($ccId)
    {
        if (!is_numeric($ccId) || $ccId <= 0 ) {
            return false;
        }

        try{
            $ccObj = new \CreditCardDao();
            $resultSet = $ccObj->getById($ccId);

            if ( !empty($resultSet) ) {
                return true;
            }
        } catch (\Exception $e) {
            Api::restLogException($e);
        }

        return false;
    }

    /**
     * This method will ensure that zip code is of 5-9 chars ONLY
     * ref: http://en.wikipedia.org/wiki/ZIP_code#ZIP.2B4
     * Cannot perform ZERO check, as combination of ZERO is acceptable i.e. 00000
     *
     * @param $zipCode
     * @return bool
     */
    public static function checkZipCode($zipCode)
    {
        if (!is_numeric($zipCode)) {
            return false;
        }

        $zipCode = (String) $zipCode;
        if (strlen($zipCode) >= 5 && strlen($zipCode) <= 9) {
            return true;
        }

        return false;
    }

    /**
     * This method will ensure that amount is greater than OR equal to 5
     *
     * @param $amount
     * @return bool
     */
    public static function checkAmount($amount)
    {
        if (!is_numeric($amount) || !isset($amount) ) {
            return false;
        }

        if ( $amount >= 5 ) {
            return true;
        }

        return false;
    }

    /**
     * Check if given email is valid
     *
     * @param $email
     * @return bool
     */
    public static function checkEmail($email)
    {
        if (\Inspekt::isEmail($email)) {
            return true;
        }
        return false;
    }

    /**
     * @return string string to be used with WWW-Authenticate header
     * @example Basic
     * @example Digest
     * @example OAuth
     * @codingStandardsIgnoreStart
     */
    public function __getWWWAuthenticateString()
    {
        //@codingStandardsIgnoreEnd
        return $this->setWWWAuthenticateString;
    }
}
