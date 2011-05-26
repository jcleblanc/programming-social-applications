<?php
require_once "common.php";

//capture code from auth
$code = $_GET["code"];

//build access token request URI
$token_url = $access_token_endpoint . "?client_id=$key&"
           . "redirect_uri=" . urlencode($callback_url) . "&"
           . "client_secret=$secret&"
           . "code=$code";
   
//get access token & expiration - parse individual params
$token_obj = explode('&', run_curl($token_url, 'GET'));
$token = explode('=', $token_obj[0]);
$token = $token[1];

//display token
echo "<h1>ACCESS TOKEN</h1>";
var_dump($token);
echo "<br /><br />";

//construct URI to fetch profile information for current user
$friends_uri = "https://graph.facebook.com/me/friends?access_token=" . $token;

//fetch profile of current user and decode
$friends = json_decode(run_curl($friends_uri, 'GET'));

//print profile
echo "<h1>CURRENT USER FRIENDS</h1>";
var_dump($friends)
?>