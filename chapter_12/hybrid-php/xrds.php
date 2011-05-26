<?php
header('Content-Type: application/xrds+xml');
$xrd = '<?xml version="1.0" encoding="UTF-8"?><xrds:XRDS xmlns:xrds="xri://$xrds" xmlns:openid="http://openid.net/xmlns/1.0" xmlns="xri://$xrd*($v*2.0)">' .
   '<XRD>'.
   '<Service xmlns="xri://$xrd*($v*2.0)">'.
   '<Type>http://specs.openid.net/auth/2.0/return_to</Type>'.
   '<URI>http://www.jonleblanc.com/openid_test/complete.php</URI>'.
   '</Service>'.
   '</XRD>'.
   '</xrds:XRDS>';

echo $xrd;
?>