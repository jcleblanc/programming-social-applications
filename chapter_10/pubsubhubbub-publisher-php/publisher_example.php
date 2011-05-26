<?php
include("publisher.php");

//define hub and feeds
$hub = 'http://pubsubhubbub.appspot.com/';
$feeds = array('http://www.example.com/feed1.xml',
               'http://www.example.com/feed2.xml',
               'http://www.example.com/feed3.xml');

//create new subscriber
$publisher = new Publisher($hub);

//publish feeds
$response = $publisher->publish($feed);

//print response
var_dump($response);
?>