<?php
require_once("OAuth.php");

$key = "KEY HERE";
$secret = "KEY HERE";

//Build a request object from the current request
$request = OAuthRequest::from_request(null, null, $_REQUEST);
$consumer = new OAuthConsumer($key, $secret, null);

//Initialize signature method
$sig_method = new OAuthSignatureMethod_HMAC_SHA1();

//validate passed oauth signature
$signature = $_GET['oauth_signature'];
$valid_sig = $sig_method->check_signature(
    $request,
    $consumer,
    null,
    $signature
);

//check if signature check succeeded
if (!$valid_sig) {
    //SIGNATURE INVALID – Produce appropriate error message
} else{
	//SIGNATURE IS VALID – Continue with normal program execution
}
?>

