<?php
require_once "common.php";

//construct Facebook auth URI
$auth_url = $authorization_endpoint
          . "?redirect_uri=" . $callback_url
          . "&client_id=" . $key
          . "&scope=email,publish_stream,manage_pages,friends_about_me,friends_status,friends_website,friends_likes";

//forward user to Facebook auth page
header("Location: $auth_url");
?>