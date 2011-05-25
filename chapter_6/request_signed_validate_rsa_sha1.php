<?php
require_once("OAuth.php");
 
class buildSignatureMethod extends OAuthSignatureMethod_RSA_SHA1 {
    public function fetch_public_cert(&$request) {
        return file_get_contents("http://www.fmodules.com/public080813.crt");
    }
}
 
//construct request method based on POST and GET parameters
$request = OAuthRequest::from_request(null, null, array_merge($_GET, $_POST));
 
//create new signature method from created class & public key certificate
$signature_method = new buildSignatureMethod();
 
//validate signature
@$signature_valid = $signature_method->check_signature($request, null, null, $_GET["oauth_signature"]);

$response = array(); 
if ($signature_valid) {
    //validated signed request - send valid message
    $response['validation'] = "valid";
} else {
    //invalid signed request - send invalid message
    $response['validation'] = "invalid";
}
 
//print response object
print(json_encode($response));
?>

