<?php
error_reporting(E_ERROR);
session_start();
require_once("includes.php");

$filters = array(  
    'openid_ax_value_email' => FILTER_SANITIZE_ENCODED,
    'openid_identity' => FILTER_SANITIZE_ENCODED,
    'openid_oauth_request_token' => FILTER_SANITIZE_ENCODED
);
$attributes = filter_var_array($_REQUEST, $filters);

$consumer = get_consumer();

//complete openid process using current app root
$return_url = sprintf("http://%s%s/complete.php", $_SERVER['SERVER_NAME'], dirname($_SERVER['PHP_SELF']));
$response = $consumer->complete($return_url);

//response state - authentication cancelled
if ($response->status == Auth_OpenID_CANCEL) {
    $response_state = 'OpenID authentication was cancelled';
//response state - authentication failed
} else if ($response->status == Auth_OpenID_FAILURE) {
    $response_state = "OpenID authentication failed: " . $response->message;
//response state - authentication succeeded
} else if ($response->status == Auth_OpenID_SUCCESS) {
    //get the identity url and capture success message
    $openid = htmlentities($response->getDisplayIdentifier());
    $response_state = sprintf('OpenID authentication succeeded: <a href="%s">%s</a>', $openid, $openid);

    if ($response->endpoint->canonicalID){
        $response_state .= '<br />XRI CanonicalID Included: ' . htmlentities($response->endpoint->canonicalID);
    }

    //get attribute exchange return values
    $response_ax = new Auth_OpenID_AX_FetchResponse();
    $ax_return = $response_ax->fromSuccessResponse($response);
    foreach ($ax_return->data as $item => $value){
        $response_state .= "<br />AX returned <b>$item</b> with the value: <b>{$value[0]}</b>";
    }
    
    //if pre-approved request token available, start OAuth process at step 4
    //reference: http://developer.yahoo.com/oauth/guide/request-token.html
    if(isset($attributes['openid_oauth_request_token'])){
        $consumer = new OAuthConsumer(CONSUMER_KEY, CONSUMER_SECRET, APP_ID);
        $sig_method = new OAuthSignatureMethod_HMAC_SHA1();
        
        //manually generate request token object
        $req_token = new stdclass();
        $req_token->key = $attributes['openid_oauth_request_token'];
        $req_token->secret = '';
        
        //generate access token object
        $acc_req = OAuthRequest::from_consumer_and_token($consumer, $req_token, "GET", $oauth_access_token_endpoint, array());
        $acc_req->sign_request($sig_method, $consumer, $req_token);
        $access_ret = run_curl($acc_req->to_url(), 'GET');
        
        //if access token fetch succeeded, we should have oauth_token and oauth_token_secret
        //parse and generate access consumer from values
        $access_token = array();
        parse_str($access_ret, $access_token);
        $access_consumer = new OAuthConsumer($access_token['oauth_token'], $access_token['oauth_token_secret'], NULL);
        
        //build profile GET request URL
        $guid = $access_token['xoauth_yahoo_guid'];
        $url = sprintf("http://%s/v1/user/%s/profile",
            'social.yahooapis.com', 
            $guid 
        );
        
        //build and sign request
        $request = OAuthRequest::from_consumer_and_token($consumer, 
            $access_consumer, 
            'GET',
            $url, 
            array());
        $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(),
            $consumer, 
            $access_consumer
        );
        
        //make GET request
        $resp = run_curl($request->to_url(), 'GET');
        
        //if debug mode, dump signatures & headers from OpenID / OAuth process 
        if ($debug){
            $debug_out = array('OpenID Response' => $response_state,
                               'Access token'    => $access_token,
                               'GET URL'         => $url,
                               'GET response'    => htmlentities($resp));
            
            print_r($debug_out);
        }
    }
}
?>