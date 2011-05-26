import os
import cgi
import time
import urllib
import oauth.oauth as oauth
import common
import Cookie

'''
' Function: Main
' Description: 
'''
def main():
    #build base consumer object with oauth keys and sign using HMAC-SHA1
    base_consumer = oauth.OAuthConsumer(common.consumer_key, common.consumer_secret)
    signature_method_hmac_sha1 = oauth.OAuthSignatureMethod_HMAC_SHA1()

    #create and sign request token fetch request object
    request_rt = oauth.OAuthRequest.from_consumer_and_token(base_consumer, callback=common.callback_url, http_url=common.request_token_endpoint)
    request_rt.sign_request(signature_method_hmac_sha1, base_consumer, None)    

    #obtain request token
    token_read = urllib.urlopen(request_rt.to_url())
    token_string = token_read.read()
    
    #parse request token into individual parameters
    token_params = cgi.parse_qs(token_string)
    oauth_token = token_params['oauth_token'][0]
    oauth_token_secret = token_params['oauth_token_secret'][0]

    #generate cookie with request token key and secret to pass through authorization process
    cookie = Cookie.Cookie()
    cookie_token = 'token=%s&token_secret=%s' % (oauth_token, oauth_token_secret)
    cookie['request_token'] = cookie_token
    cookie['timestamp'] = time.time()
    print cookie
    
    #redirect user to authorization endpoint
    print "Location: %s?oauth_token=%s" % (common.authorize_endpoint, oauth_token)

if __name__ == '__main__':
    main()