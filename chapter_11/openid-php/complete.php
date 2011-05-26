<?php
error_reporting(E_ERROR);
require_once("includes.php");

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

    echo "<h1>RESPONSE</h1>";
    var_dump($response);
    echo "<br /><br />";

    if ($response->endpoint->canonicalID){
        $response_state .= '<br />XRI CanonicalID Included: ' . htmlentities($response->endpoint->canonicalID);
    }

    //display sreg return data if available
    $response_sreg = Auth_OpenID_SRegResponse::fromSuccessResponse($response)->contents();
    echo "<h1>SReg</h1>";
    var_dump(Auth_OpenID_SRegResponse::fromSuccessResponse($response));
    foreach ($response_sreg as $item => $value){
        $response_state .= "<br />SReg returned <b>$item</b> with the value: <b>$value</b>";
    }

    //display pape policy return data if available
	$response_pape = Auth_OpenID_PAPE_Response::fromSuccessResponse($response);
	if ($response_pape){
        //pape policies affected by authentication 
        if ($response_pape->auth_policies){
            $response_state .= "<br />PAPE returned policies which affected the authentication:";

            foreach ($response_pape->auth_policies as $uri){
                $response_state .= '- ' . htmlentities($uri);
            }
        }
        
        //server authentication age
        if ($response_pape->auth_age){
            $response_state .= "<br />PAPE returned server authentication age with the value: " .
                                htmlentities($response_pape->auth_age);
        }
        
        //nist authentication level
        if ($response_pape->nist_auth_level) {
            $response_state .= "<br />PAPE returned server NIST auth level with the value: " .
                                htmlentities($response_pape->nist_auth_level);
        }

	}

    //get attribute exchange return values
    $response_ax = new Auth_OpenID_AX_FetchResponse();
    $ax_return = $response_ax->fromSuccessResponse($response);
    echo "<h1>AX</h1>";
    var_dump($ax_return);
    foreach ($ax_return->data as $item => $value){
        $response_state .= "<br />AX returned <b>$item</b> with the value: <b>{$value[0]}</b>";
    }
}
    
print $response_state;
?>