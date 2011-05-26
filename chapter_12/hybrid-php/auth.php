<?php
error_reporting(E_ERROR);
session_start();

require_once "includes.php";  //configurations and common functions

/******************************************************************
 * Function: Make Request
 * Description: Builds out the OpenID request using the defined
 *              request extensions
 ******************************************************************/
function make_request(){
    //get openid identifier URL
    if (empty($_GET['openid_url'])) {
        $error = "Expected an OpenID URL.";
        print $error;
        exit(0);
    }
    
    $openid = $_GET['openid_url'];
    $consumer = get_consumer();
    
    //begin openid authentication
    $auth_request = $consumer->begin($openid);
    
    //no authentication available
    if (!$auth_request) {
        print "Authentication error; not a valid OpenID.";
    }
    
    //add openid extensions to the request
    $auth_request->addExtension(attach_ax());    //attribute exchange
    
    //generate redirect url
    $return_url = sprintf("%s%s", APP_ROOT, FILE_COMPLETE);
    $trust_root = sprintf("http://%s%s/", $_SERVER['SERVER_NAME'], dirname($_SERVER['PHP_SELF']));
    $redirect_url = $auth_request->redirectURL($trust_root, $return_url);
    
    //attach oauth extension parameters to redirect url
    $hybrid_fields = array(
        'openid.ns.oauth' => 'http://specs.openid.net/extensions/oauth/1.0',
        'openid.oauth.consumer' => CONSUMER_KEY
    );
    $redirect_url .= '&'.http_build_query($hybrid_fields);
        
    //if no redirect available display error message, else redirect
    if (Auth_OpenID::isFailure($redirect_url)) { print "Could not redirect to server: " . $redirect_url->message; }
    else { header("Location: " . $redirect_url); }
}

/******************************************************************
 * Function: Attach Attribute Exchange
 * Description: Creates attribute exchange OpenID extension request
 *              to allow capturing of extended profile attributes
 ******************************************************************/
function attach_ax(){
    //build attribute request list
    $attribute[] = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/contact/email', 1, 1, 'email');
    //$attribute[] = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/namePerson', 1, 1, 'fullname');
    //$attribute[] = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/person/gender', 1, 1, 'gender');
    //$attribute[] = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/media/image/default', 1, 1, 'picture');
    
    //create attribute exchange request
    $ax = new Auth_OpenID_AX_FetchRequest;
    
    //add attributes to ax request
    foreach($attribute as $attr){
        $ax->add($attr);
    }
    
    //return ax request
    return $ax;
}

//initiate the OpenID request
make_request();
?>