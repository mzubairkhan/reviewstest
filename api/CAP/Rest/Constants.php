<?php

namespace CAP\Rest;


class Constants
{

    const AUTHENTICATION_CLASS_NAME = '\CAP\Rest\Auth';

    const DATETIME_UTC = "UTC";

    const ENCRYPTION_METHOD = "sha256";

    const QUERY_LIMIT_TEN = 10;

    const RESPONSE_STATUS        = "status";
    const RESPONSE_RESULT_SET    = "resultSet";
    const RESPONSE_TOTAL_RECORDS = "totalRecords";
    const RESPONSE_STATUS_MESSAGE= "statusMessage";

    const STATUS_SUCCESSFUL = "Successful";
    const STATUS_DENIED     = "Denied";
    const STATUS_INVALID    = "Invalid";
    const STATUS_FAILED     = "Failed";

    const SERVICE_TIMESTAMP_LIMIT = 15;

    const STR_SIGNATURE = "signature";
    const STR_TIMESTAMP = "timestamp";
    const STR_APIKEY = "apiKey";

    const API_CURRENT =  1;
    const API_MINIMUM =  1;

    const CAMPAIGN_TYPE_RENEWAL = 'renewal';
    const CAMPAIGN_TYPE_RENEWAL_1 = 'renewal_1';

}