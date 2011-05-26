<?php
include("subscriber.php");

//define hub, callback and feed
$hub = 'http://pubsubhubbub.appspot.com/';
$callback = 'http://www.example.com/publish';
$feed = 'http://www.example.com';

//create new subscriber
$subscriber = new Subscriber($hub, $callback);

//subscribe / unsubscribe methods
$response = $subscriber->subscribe($feed);
//$response = $subscriber->unsubscribe($feed);

//print response
var_dump($response);
?>