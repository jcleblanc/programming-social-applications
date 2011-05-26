import os

#set oauth consumer / secret keys and application id
consumer_key = 'YOUR KEY HERE'
consumer_secret = 'YOUR SECRET HERE'
appid = 'YOUR APPLICATION ID HERE'

#application urls
callback_url = 'http://%s/complete.py' % (os.environ['HTTP_HOST'])

#oauth access token endpoints (Yahoo!)
request_token_endpoint = 'https://api.login.yahoo.com/oauth/v2/get_request_token'
authorize_endpoint = 'https://api.login.yahoo.com/oauth/v2/request_auth'
oauth_access_token_endpoint = 'https://api.login.yahoo.com/oauth/v2/get_token'
