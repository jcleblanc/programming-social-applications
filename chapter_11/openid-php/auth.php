<?php
error_reporting(E_ERROR);
ini_set("display_errors", 1);
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
        echo "Authentication error; not a valid OpenID.";
    }
    
    //add openid extensions to the request
    $auth_request->addExtension(attach_ax());    //attribute exchange
    $auth_request->addExtension(attach_sreg());  //simple registration
    $auth_request->addExtension(attach_pape());  //pape policies
    
    $return_url = sprintf("http://%s%s/%s", $_SERVER['SERVER_NAME'], dirname($_SERVER['PHP_SELF']), FILE_COMPLETE);
    $trust_root = sprintf("http://%s%s/", $_SERVER['SERVER_NAME'], dirname($_SERVER['PHP_SELF']));
    
    //openid v1 - send through redirect
    if ($auth_request->shouldSendRedirect()){
        $redirect_url = $auth_request->redirectURL($trust_root, $return_url);
    
        //if no redirect available display error message, else redirect
        if (Auth_OpenID::isFailure($redirect_url)) { print "Could not redirect to server: " . $redirect_url->message; }
        else { header("Location: " . $redirect_url); }
        
    //openid v2 - use javascript form to send POST to server
    } else {
        //build form markup
        $form_id = 'openid_message';
        $form_html = $auth_request->htmlMarkup($trust_root, $return_url, false, array('id' => $form_id));
    
        //if markup cannot be built display error, else render form
        if (Auth_OpenID::isFailure($form_html)) { print "Could not redirect to server: " . $form_html->message; }
        else { print $form_html; }
    }
}

/******************************************************************
 * Function: Attach Attribute Exchange
 * Description: Creates attribute exchange OpenID extension request
 *              to allow capturing of extended profile attributes
 ******************************************************************/
function attach_ax(){
    //build attribute request list
    $attribute[] = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/contact/email', 1, 1, 'email');
    $attribute[] = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/namePerson', 1, 1, 'fullname');
    $attribute[] = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/person/gender', 1, 1, 'gender');
    $attribute[] = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/media/image/default', 1, 1, 'picture');
    
    //create attribute exchange request
    $ax = new Auth_OpenID_AX_FetchRequest;
    
    //add attributes to ax request
    foreach($attribute as $attr){
        $ax->add($attr);
    }
    
    //return ax request
    return $ax;
}

/******************************************************************
 * Function: Attach Simple Registration
 * Description: Creates simple registration OpenID extension request
 *              to allow capturing of simple profile attributes
 ******************************************************************/
function attach_sreg(){
    //create simple registration request
    $sreg_request = Auth_OpenID_SRegRequest::build(
        array('nickname'),
        array('fullname', 'email'));

    //return sreg request
    return $sreg_request;
}

/******************************************************************
 * Function: Attach PAPE 
 * Description: Creates PAPE policy OpenID extension request to
 *              inform server of policy standards
 ******************************************************************/
function attach_pape(){
    //capture pape policies passed in via openid form
    $policy_uris = $_GET['policies'];
    
    //create pape policy request
    $pape_request = new Auth_OpenID_PAPE_Request($policy_uris);
    
    //return pape request
    return $pape_request;
}

//initiate the OpenID request
make_request();
?>