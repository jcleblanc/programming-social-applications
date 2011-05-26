import os
import cgi
import sys
import Cookie
import urllib
import oauth.oauth as oauth
import common

'''
' Class: Dot Notation Insertion
' Description: Adds dot notation capabilities to a dictionary
'''
class dotdict(dict):
    def __getattr__(self, attr):
        return self.get(attr, None)
    __setattr__= dict.__setitem__
    __delattr__= dict.__delitem__

'''
' Function: Main
' Description: 
'''
def main():
    #create new smart cookie to extract request token
    cookie = Cookie.SmartCookie()
    
    #if a cookie is available, load it
    if os.environ.has_key('HTTP_COOKIE'):
        cookie.load(os.environ['HTTP_COOKIE'])
        
        #if the request token cookie is available, load and parse it
        if cookie.has_key('request_token'):
            request_token = cookie.get('request_token').value
            rt_params = cgi.parse_qs(request_token)
            
            #parse query string parameters into dictionary
            qs_params = {}
            string_split = [s for s in os.environ['QUERY_STRING'].split('&') if s]
            for item in string_split:
                key,value = item.split('=')
                qs_params[key] = value
            
            #create base consumer and signature method objects
            base_consumer = oauth.OAuthConsumer(common.consumer_key, common.consumer_secret)
            signature_method_hmac_sha1 = oauth.OAuthSignatureMethod_HMAC_SHA1()
            
            #build dictionary of request token and secret to exchange for access token
            req_token = dotdict({'key': rt_params['token'][0], 'secret': rt_params['token_secret'][0]})
        
            #build request token to access token exchange request object and sign it
            oauth_request = oauth.OAuthRequest.from_consumer_and_token(base_consumer, token=req_token, verifier=qs_params['oauth_verifier'], http_url=common.oauth_access_token_endpoint)
            oauth_request.sign_request(signature_method_hmac_sha1, base_consumer, req_token)
            
            #obtain request token as string and dictionary objects
            token_read = urllib.urlopen(oauth_request.to_url())
            token_string = token_read.read()
            token_params = cgi.parse_qs(token_string)
            
            #create access token object out of access token string
            access_token = oauth.OAuthToken.from_string(token_string)
            
            #create url to Yahoo! servers to access user profile
            guid = token_params['xoauth_yahoo_guid'][0]
            url = 'http://%s/v1/user/%s/profile' % ('social.yahooapis.com', guid)
            
            #create new oauth request and sign using HMAC-SHA1 to get profile of authorized user
            oauth_request = oauth.OAuthRequest.from_consumer_and_token(base_consumer, token=access_token, http_method='GET', http_url=url)
            oauth_request.sign_request(signature_method_hmac_sha1, base_consumer, access_token)
            
            #make request to get profile of user
            profile_read = urllib.urlopen(oauth_request.to_url())
            profile_string = profile_read.read()
            print 'Content-Type: text/plain'
            print ''
            print profile_string
        else:
            #if request token cookie was not available, end
            print 'Request token cookie not found - exiting'
            sys.exit()
    else:
        #if cookies were not available, end
        print 'Request token cookie not found - exiting'
        sys.exit()
    
if __name__ == '__main__':
    main()