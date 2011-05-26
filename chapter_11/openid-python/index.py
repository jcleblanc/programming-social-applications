print '''\
Content-type: text/html; charset=UTF-8
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>OpenID Sample Application</title>
</head>
<body>
<style>
form{ font:12px arial,helvetica,sans-serif; }
#openid_url { background:#FFFFFF url(http://wiki.openid.net/f/openid-16x16.gif) no-repeat scroll 5px 50%; padding-left:25px; }
</style>

<form action="auth.py" method="GET">
    <input type="hidden" value="login" name="actionType">
    <h2>Sign in using OpenID</h2>
    <input type="text" style="font-size: 12px;" value="" size="40" id="openid_url" name="openid_url"> &nbsp;
    <input type="submit" value="Sign in"> <br><small>(e.g. http://username.myopenid.com)</small><br /><br />
    
    <b>PAPE Policies (Optional)</b><br />
    <input type="checkbox" name="policy_phishing" value="PAPE_AUTH_PHISHING_RESISTANT" /> PAPE_AUTH_PHISHING_RESISTANT<br />
</form>		  

</body></html>
'''