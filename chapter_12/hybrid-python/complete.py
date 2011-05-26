import cgi
import openid
import urllib
import os
import oauth.oauth as oauth

import common

from openid.consumer import consumer
from openid.extensions import ax

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
' Description: Completes OpenID authentication process and prints results
'''
def main():
    #create a base consumer object
    oidconsumer = consumer.Consumer({}, None)
    
    #print page content type
    print 'Content-Type: text/plain'
    print ''
    
    #parse query string parameters into dictionary
    params = {}
    string_split = [s for s in os.environ['QUERY_STRING'].split('&') if s]
    for item in string_split:
        key,value = item.split('=')
        if key != 'domain_unverified':
            params[key] = urllib.unquote(value)
    
    #complete OpenID authentication and get identifier
    info = oidconsumer.complete(params, common.return_to)
    display_identifier = info.getDisplayIdentifier()
    
    #build attribute exchange response object
    ax_response = ax.FetchResponse.fromSuccessResponse(info)
    if ax_response:
        ax_items = {
            'email': ax_response.get('http://axschema.org/contact/email'),
            'picture': ax_response.get('http://axschema.org/media/image/default')
        }
        
        #print attribute exchange object
        print 'Attribute Exchange Response Object:'
        print ax_items
    
    #print openid display identifier
    print '\n\nOpenID Display Identifier: \n' + display_identifier
    
    #check the openid return status
    if info.status == consumer.FAILURE and display_identifier:
        message = "\n\nOpenID Response:\nVerification failed"
    elif info.status == consumer.CANCEL:
        message = '\n\nOpenID Response:\nVerification cancelled'
    elif info.status == consumer.SETUP_NEEDED:
        message = '\n\nOpenID Response:\nSetup needed'
    elif info.status == consumer.SUCCESS:
        message = '\n\nOpenID Response:\nSuccess'
        
        #build base consumer object with oauth keys and sign using HMAC-SHA1
        base_consumer = oauth.OAuthConsumer(common.consumer_key, common.consumer_secret)
        signature_method_hmac_sha1 = oauth.OAuthSignatureMethod_HMAC_SHA1()
        
        #build dictionary of pre-authorized request token and blank secret to exchange for access token
        req_token = dotdict({'key': params['openid.oauth.request_token'], 'secret': ''})
        
        #create new oauth request and sign using HMAC-SHA1 to access token endpoint to exchange request token for access token
        oauth_request = oauth.OAuthRequest.from_consumer_and_token(base_consumer, token=req_token, verifier=None, http_url=common.oauth_access_token_endpoint)
        oauth_request.sign_request(signature_method_hmac_sha1, base_consumer, req_token)
        
        #make request to exchange request token for access token string
        token_read = urllib.urlopen(oauth_request.to_url())
        token_string = token_read.read()
        
        #parse access token string into parameters and extract user guid
        token_params = cgi.parse_qs(token_string)
        guid = token_params['xoauth_yahoo_guid'][0]
        
        #create new access token object to make permissioned requests
        access_token = oauth.OAuthToken.from_string(token_string)
        
        #create url to Yahoo! servers to access user profile
        url = 'http://%s/v1/user/%s/profile' % ('social.yahooapis.com', guid)
        
        #create new oauth request and sign using HMAC-SHA1 to get profile of authorized user
        oauth_request = oauth.OAuthRequest.from_consumer_and_token(base_consumer, token=access_token, http_method='GET', http_url=url)
        oauth_request.sign_request(signature_method_hmac_sha1, base_consumer, access_token)
        
        #make request to get profile of user
        profile_read = urllib.urlopen(oauth_request.to_url())
        profile_string = profile_read.read()
        
        #print profile response object
        message += '\n\nProfile Response Object:\n' + profile_string
    else:
        message = '\n\nOpenID Response:\nVerification failed.'
    print message

if __name__ == '__main__':
    main()