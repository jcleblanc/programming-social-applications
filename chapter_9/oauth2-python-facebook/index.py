import cgi
import urllib
import urllib2
import json

#client OAuth keys
key = 'KEY HERE'
secret = 'KEY HERE'

#Facebook URIs and application callbacks
callback_url = "http://oauth2-facebook.appspot.com/index.py"             #INSERT CALLBACK URL HERE
authorization_endpoint = "https://graph.facebook.com/oauth/authorize"
access_token_endpoint = "https://graph.facebook.com/oauth/access_token"

#get query string parameters
params = cgi.FieldStorage()

"""
" If a code parameter is available in the query string then the user
" has given the client permission to access their protected data.
" If not, the script should forward the user to log in and accept
" the application permissions.
"""
if params.has_key('code'):
    code = params['code'].value
    
    #build access token request URI
    token_url = "%s?client_id=%s&redirect_uri=%s&client_secret=%s&code=%s" % (access_token_endpoint, key, urllib.quote_plus(callback_url), secret, code)
    
    #make request to capture access token
    f = urllib.urlopen(token_url)
    token_string = f.read()
    
    #split token string to obtain the access token object
    token_obj = token_string.split('&')
    access_token_obj = token_obj[0].split('=')
    access_token = access_token_obj[1]
    
    #construct URI to fetch friend information for current user
    friends_uri = "https://graph.facebook.com/me/friends?access_token=%s" % (access_token)
    
    #fetch friends of current user and decode
    request = urllib.urlopen(friends_uri)
    friends = json.read(request.read())
    
    #display access token and friends
    print 'Content-Type: text/plain'
    print ''
    print "<h1>Friends</h1>"
    print access_token
    print "<br /><br />"
    print friends
else:
    #construct Facebook authorization URI
    auth_url = authorization_endpoint + "?redirect_uri=" + callback_url + "&client_id=" + key + "&scope=email,publish_stream,manage_pages,friends_about_me,friends_status,friends_website,friends_likes"
    
    #redirect the user to the Facebook authorization URI
    print "Location: " + auth_url
