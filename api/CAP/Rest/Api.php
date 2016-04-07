<?php

namespace CAP\Rest;

use Luracast\Restler\Restler;

class Api
{
    /**
     * Array of variables used throughout Restler
     * For constructing $_REQUEST (since we have no GLOBAL variable for PUT | DELETE)
     * $_GET is utilized by Rest, hence discouraged
     *
     * @var array
     */
    public static $getArray = array(
        'signature'
    );

    /**
     * @param $arrayOfObjects
     * @param string $str
     * @return array
     */
    public static function apiResponse($arrayOfObjects, $str = Constants::STATUS_FAILED)
    {
        return array(
            Constants::RESPONSE_STATUS => $str,
            Constants::RESPONSE_RESULT_SET => $arrayOfObjects,
            Constants::RESPONSE_TOTAL_RECORDS => count($arrayOfObjects)
        );
    }

    /**
     * Loader method for all the class-names, offering webservices
     *
     * @param Restler $rest
     * @param $version
     */
    public static function loadClasses(Restler $rest)
    {
        // All required files must have namespaces
        $rest->addAPIClass('CAP\\Rest\\User');
        $rest->addAPIClass('CAP\\Rest\\Client');
        $rest->addAPIClass('CAP\\Rest\\Acl');
        $rest->addAPIClass('CAP\\Rest\\Secure');
        $rest->addAPIClass('CAP\\Rest\\Page');
        $rest->addAPIClass('CAP\\Rest\\Provider');
        $rest->addAPIClass('CAP\\Rest\\CustomField');
        $rest->addAPIClass('CAP\\Rest\\Website');
        $rest->addAPIClass('CAP\\Rest\\GitRepo');
        $rest->addAPIClass('CAP\\Rest\\Media');
    }

    /**
     * Log exceptions thrown by REST API
     *
     * @param \Exception $exc
     */
    public static function restLogException(\Exception $exc)
    {
        self::getLog()->write("Error[{$exc->getCode() }] {$exc->getMessage() }\n{$exc->getTraceAsString() }");
    }

    /**
     * Append log file using string
     * @param $str
     */
    public static function restLog($str)
    {
        self::getLog()->write($str);
    }

    /**
     * Get log file object
     *
     * @return \Log4PHP
     */
    public static function getLog()
    {
        return new \Log4PHP(Config::getConfiguration()->log->path->debug);
    }

    /**
     * Authentication of the defined / compulsory params at the beginning of each web-service
     * @param $inputArray
     * @param $validateKey
     */
    public static function validateRequestData($inputArray, $validateKey)
    {
        if ( empty($inputArray) ) {
            self::invalidResponse(
                "Invalid input.",
                400,
                Constants::STATUS_INVALID,
                null,
                true);
        }

        foreach ($validateKey as $validKey=>$condition) {

            if ( !isset($inputArray[$validKey]) || !array_key_exists($validKey, $inputArray) ) {

                Api::restLog("Failed::Given: " . $validKey .
                    " [" . $condition . "] => " . $inputArray[$validKey]);

                self::invalidResponse("Invalid {$validKey}.",
                    400,
                    Constants::STATUS_INVALID,
                    null,
                    true);
            }

            if ( isset($condition) ) {

                // if encounter a static method for verification of conditions
                if ( !is_object($condition) && strstr($condition, '::') ) {

                    $conditionBreakdown= explode('::', $condition);
                    $obj = $conditionBreakdown[0];
                    $method_name = $conditionBreakdown[1];

                }

                if (is_object($condition) && is_callable($condition)) {
                    if ($condition($inputArray[$validKey])) {
                        continue;
                    } else {
                        self::invalidResponse("Anonymous condition failed for :: ". $inputArray[$validKey],
                            400,
                            Constants::STATUS_INVALID,
                            null,
                            true
                        );
                    }
                } else if ( function_exists($condition) ) {

                    if (call_user_func($condition, $inputArray[$validKey])) {

                        continue;

                    } else {

                        self::invalidResponse("Condition {$condition} failed for :: ". $inputArray[$validKey],
                            400,
                            Constants::STATUS_INVALID,
                            null,
                            true);

                    }

                } else if ( class_exists(__NAMESPACE__ . '\\' . $obj, true)
                    && method_exists(__NAMESPACE__ . '\\' . $obj, $method_name)
                ) {

                    if ( call_user_func(__NAMESPACE__ .'\\'.$condition, $inputArray[$validKey]) ) {

                        continue;

                    } else {

                        self::invalidResponse("Condition {$condition} failed for :: ". $inputArray[$validKey],
                            400,
                            Constants::STATUS_INVALID,
                            null,
                            true);

                    }

                } else {

                    // Safe-check, because of namespaces, we had to change logic
                    Api::restLog(__METHOD__ . "::Triggering safe-check. This should not happen, see logs");
                    Api::restLog(print_r($inputArray, true));
                    Api::restLog(print_r($validateKey, true));

                    self::invalidResponse("Safe-check triggered: see logs",
                        400,
                        Constants::STATUS_INVALID,
                        null,
                        true);
                }
            }
        }
    }

    /**
     * All invalid response for web-services must go through this method
     *
     * @param $reason
     * @param int $errorCode
     * @param string $errorString
     * @param string $exception
     * @param bool $forceExit
     * @param bool $addReason
     * @return array
     */
    public static function invalidResponse(
        $reason,
        $errorCode=400,
        $errorString = Constants::STATUS_INVALID,
        $exception="",
        $forceExit=false,
        $addReason=false
    ) {

        /*if (!empty($exception)) {
            Api::restLogException($exception);
        }

        Api::restLog($errorCode . "::" . $reason);*/

        $ret = array(
            Constants::RESPONSE_STATUS => $errorString,
            Constants::RESPONSE_RESULT_SET => null,
            Constants::RESPONSE_TOTAL_RECORDS => 0
        );

        //add reason to response if explicitly specified
        if ($addReason) {
            $ret[Constants::RESPONSE_STATUS_MESSAGE] = $reason;
        }

        //force the response to be sent
        // mainly used in AUTH methods
        if ( $forceExit ) {
            self::rawResponse($ret, $errorCode);
        }
        return $ret;
    }

    /**
     * All denied response for web-services must go through this method
     *
     * @param $reason
     * @param int $errorCode
     * @param string $errorString
     * @param string $exception
     * @param bool $forceExit
     * @param bool $addReason
     * @return array
     */
    public static function deniedResponse(
        $reason,
        $errorCode=403,
        $errorString = Constants::STATUS_DENIED,
        $exception="",
        $forceExit=false,
        $addReason=false
    ) {

        /*if (!empty($exception)) {
            Api::restLogException($exception);
        }

        Api::restLog($errorCode . "::" . $reason);*/
        $ret = array(
            Constants::RESPONSE_STATUS => $errorString,
            Constants::RESPONSE_RESULT_SET => array(),
            Constants::RESPONSE_TOTAL_RECORDS => 0
        );

        //add reason to response if explicitly specified
        if ($addReason) {
            $ret[Constants::RESPONSE_STATUS_MESSAGE] = $reason;
        }

        //force the response to be sent
        // mainly used in AUTH methods
        if ( $forceExit ) {
            self::rawResponse($ret, $errorCode);
        }
        return $ret;
    }

    /**
     * All failed response for web-services must go through this method
     *
     * @param $reason
     * @param int $errorCode
     * @param string $errorString
     * @param string $exception
     * @param bool $forceExit
     * @param bool $addReason
     * @return array
     */
    public static function failedResponse(
        $reason,
        $errorCode=500,
        $errorString = Constants::STATUS_FAILED,
        $exception="",
        $forceExit=false,
        $addReason=false
    ) {

        /*if (!empty($exception)) {
            Api::restLogException($exception);
        }

        Api::restLog($errorCode . "::" . $reason);*/

        $ret = array(
            Constants::RESPONSE_STATUS => $errorString,
            Constants::RESPONSE_RESULT_SET => array(),
            Constants::RESPONSE_TOTAL_RECORDS => 0
        );

        //add reason to response if explicitly specified
        if ($addReason) {
            $ret[Constants::RESPONSE_STATUS_MESSAGE] = $reason;
        }

        //force the response to be sent
        // mainly used in AUTH methods
        if ( $forceExit ) {
            self::rawResponse($ret, $errorCode);
        }
        return $ret;
    }

    /**
     * Used for exiting gracefully when exception is generated or authentication failure occurs
     * $errorCode added to handle 500 httpcode set by payment exception
     *
     * @param $str
     * @param $errorCode
     */
    public static function rawResponse($str, $errorCode)
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($errorCode);
        exit(json_encode($str));
    }
}
