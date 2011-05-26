import os

#set oauth consumer key and consumer secret keys
consumer_key = 'KEY HERE'
consumer_secret = 'KEY HERE'

#oauth access token endpoint (Yahoo!)
oauth_access_token_endpoint = 'https://api.login.yahoo.com/oauth/v2/get_token'

#trust root and return to urls for openid process
trust_root = 'http://%s/' % (os.environ['HTTP_HOST'])
return_to = 'http://%s/complete.py' % (os.environ['HTTP_HOST'])
