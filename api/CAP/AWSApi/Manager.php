<?php

namespace CAP\AWSApi;

use Aws\Rds\RdsClient;
use Aws\Rds\Exception;
use Aws\Common\Credentials\Credentials;
use Aws\Common\Enum;
use Aws\S3\S3Client;
use CAP\Core\Common;

Class Manager {

    private $credentials, $client, $aws_settings;
    public $region;
    private $cons_params;

    function __construct($params) {
        $this->cons_params = $params;
        $settings = Common::getSettings();
        $key = $settings['aws-s3']['key'];
        $secret = $settings['aws-s3']['secret'];
        $this->credentials = new Credentials($key, $secret);

        // Instantiate the client with your AWS credentials
        $this->client = RdsClient::factory(array(
            'credentials' => $this->credentials,
            'region' => $params['region']
        ));
    }

    public function getList() {
        // Instantiate the client with your AWS credentials
        $client = RdsClient::factory(array(
            'credentials' => $this->credentials,
            'region' => \Aws\Common\Enum\Region::US_WEST_2
        ));

        /*
          //
          $result = $client->describeDBSecurityGroups(
          array(
          'DBSecurityGroupName' => 'tariq-test'
          )
          ); */
        /* $result = $client->authorizeDBSecurityGroupIngress(array(
          // DBSecurityGroupName is required
          'DBSecurityGroupName' => 'tariq-test',
          'CIDRIP' => '127.0.0.1/32'
          )); */

        $result = $client->describeDBInstances(array());
        Common::dumpAndDie($result);
    }

    public function s3Uploadjson($sBucketName, $sFileName, $aBody) {

        $this->loads3config();

        $aResponse = array();

        // Instantiate the client.
        $oS3 = S3Client::factory(array(
            'credentials' => $this->credentials
        ));

        // Version Checking
        try {

            $settings = Common::getSettings();

            // 1. get version.json data
            $oVersion = file_get_contents('https://s3.amazonaws.com/' . $sBucketName . '/' . $settings[$this->aws_settings]['s3_version_file_name']);

            if (!empty($oVersion)) {

                // 2. decode json
                $aVersionData = json_decode($oVersion, true);

                if (is_array($aVersionData) && isset($aVersionData['version'][$sFileName])) {

                    // 3. Increment by 1 to the respective file
                    $aVersionData['version'][$sFileName] = $aVersionData['version'][$sFileName] + 1;

                    // 4. Update Version in the respective file
                    $aBody['header']['version'] = (string) $aVersionData['version'][$sFileName];

                    $aResponse['current_version'] = $aVersionData['version'][$sFileName];
                    $aResponse['data'] = json_encode($aBody);

                    //Upload version.json to S3
                    $oS3->putObject(array(
                        'Bucket' => $sBucketName,
                        'Key' => $settings[$this->aws_settings]['s3_version_file_name'],
                        'Body' => json_encode($aVersionData),
                        'ContentType' => 'application/json'
                    ));
                } else {
                    throw new \Exception('Unable to parse versions.json or "' . $aVersionData['version'][$sFileName] . '" key doesnt exist', 500);
                }
            } else {

                throw new \Exception('Unable to locate versions.json', 500);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }

        // Normal File Upload
        try {

            // Upload data.
            $result = $oS3->putObject(array(
                'Bucket' => $sBucketName,
                'Key' => $sFileName,
                'Body' => json_encode($aBody),
                'ContentType' => 'application/json'
            ));

            $aResponse['ETag'] = $result['ETag'];
            $aResponse['VersionId'] = $result['VersionId'];
            $aResponse['RequestId'] = $result['RequestId'];
            $aResponse['ObjectURL'] = $result['ObjectURL'];

            return $aResponse;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    public function s3UploadFileFromPath($sBucketName, $sFileName, $sFilePath) {
       // $this->loads3config();

        // Instantiate the client.
        $oS3 = S3Client::factory(array(
            'credentials' => $this->credentials
        ));

        // Normal File Upload
        try {

            if (file_exists($sFilePath)) {

                $aResponse = array();

                // Upload data.
                $result = $oS3->putObject(array(
                    'Bucket' => $sBucketName,
                    'Key' => $sFileName,
                    'SourceFile' => $sFilePath
                ));

                $aResponse['ETag'] = $result['ETag'];
                $aResponse['VersionId'] = $result['VersionId'];
                $aResponse['RequestId'] = $result['RequestId'];
                $aResponse['ObjectURL'] = $result['ObjectURL'];

                return $aResponse;
            } else {

                throw new \Exception('Unable to locate ' . $sFilePath);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    private function loads3config() {
        $params = $this->cons_params;
        $settings = Common::getSettings();
        $key = $settings['aws-s3']['key'];
        $secret = $settings['aws-s3']['secret'];
        $this->credentials = new Credentials($key, $secret);
        $this->aws_settings = 'aws-s3';

        // Instantiate the client with your AWS credentials
        $this->client = RdsClient::factory(array(
            'credentials' => $this->credentials,
            'region' => $params['region']
        ));
    }

}
