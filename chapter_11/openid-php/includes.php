<?php
require_once "Auth/OpenID/Consumer.php";    //openid consumer code
require_once "Auth/OpenID/FileStore.php";   //file storage
require_once "Auth/OpenID/SReg.php";        //simple registration
require_once "Auth/OpenID/PAPE.php";        //pape policy
require_once "Auth/OpenID/AX.php";          //attribute exchange

define('APP_ROOT', 'http://www.jcleblanc.com/projects/openid-php/');
define('FILE_COMPLETE', 'complete.php');
define('STORAGE_PATH', 'php_consumer');

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
