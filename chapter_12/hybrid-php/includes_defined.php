<?php
require_once "Auth/OpenID/Consumer.php";    //openid consumer code
require_once "Auth/OpenID/FileStore.php";   //file storage
require_once "Auth/OpenID/AX.php";          //attribute exchange
require_once "OAuth.php";                   //oauth library

define('APP_ROOT', 'http://www.jonleblanc.com/openid-oauth-php/');
define('FILE_COMPLETE', 'complete.php');
define('STORAGE_PATH', 'php_consumer');

define('CONSUMER_KEY', 'dj0yJmk9YXc5bzFXcUU5OVVpJmQ9WVdrOVRFVnZWRzlFTkdjbWNHbzlNQS0tJnM9Y29uc3VtZXJzZWNyZXQmeD0wOA--');
define('CONSUMER_SECRET', 'cdafab2352296a8a4b9465d74ba6020555bb112a');
define('APP_ID', 'LEoToD4g');

$debug = true;
$base_url = "http://www.yoursite.com/complete.php";
$request_token_endpoint = 'https://api.login.yahoo.com/oauth/v2/get_request_token';
$authorize_endpoint = 'https://api.login.yahoo.com/oauth/v2/request_auth';
$oauth_access_token_endpoint = 'https://api.login.yahoo.com/oauth/v2/get_token';

/***************************************************************************
 * Function: Run CURL
 * Description: Executes a CURL request
 * Parameters: url (string) - URL to make request to
 *             method (string) - HTTP transfer method
 *             headers - HTTP transfer headers
 *             postvals - post values
 **************************************************************************/
function run_curl($url, $method = 'GET', $headers = null, $postvals = null){
    $ch = curl_init($url);
    
    if ($method == 'GET'){
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    } else {
        $options = array(
            CURLOPT_HEADER => true,
            CURLINFO_HEADER_OUT => true,
            CURLOPT_VERBOSE => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => $postvals,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_TIMEOUT => 3
        );
        curl_setopt_array($ch, $options);
    }
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return $response;
}

/******************************************************************
 * Function: Get Consumer
 * Description: Creates consumer file storage and OpenID consumer
 ******************************************************************/
function get_consumer() {
    //ensure file storage path can be created
    if (!file_exists(STORAGE_PATH) && !mkdir(STORAGE_PATH)){
        print "Could not create FileStore directory '". STORAGE_PATH ."'. Please check permissions.";
        exit(0);
    }

    //create consumer file store
    $store = new Auth_OpenID_FileStore(STORAGE_PATH);
    
    //create and return consumer
    $consumer =& new Auth_OpenID_Consumer($store);
    return $consumer;
}
?>
