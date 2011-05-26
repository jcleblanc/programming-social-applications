<?php
$key = 'b719d03bfca6cd9a91aa41995a080f8e';
$secret = 'fce944cdab7de45ef03a2dee37733ee8';

$callback_url = "http://www.jcleblanc.com/projects/oauth2-php-facebook/complete.php";
$authorization_endpoint = "https://graph.facebook.com/oauth/authorize";
$access_token_endpoint = "https://graph.facebook.com/oauth/access_token";

/***************************************************************************
 * Function: Run CURL
 * Description: Executes a CURL request
 * Parameters: url (string) - URL to make request to
 *             method (string) - HTTP transfer method
 *             headers - HTTP transfer headers
 *             postvals - post values
 **************************************************************************/
function run_curl($url, $method = 'GET', $postvals = null){
    $ch = curl_init($url);
    
    //GET request: send headers and return data transfer
    if ($method == 'GET'){
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1
        );
        curl_setopt_array($ch, $options);
    //POST / PUT request: send post object and return data transfer
    } else {
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $postvals,
            CURLOPT_RETURNTRANSFER => 1
        );
        curl_setopt_array($ch, $options);
    }
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return $response;
}
?>
