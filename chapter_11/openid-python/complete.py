import sys
import cgi
import openid
import urllib
import os

from openid.consumer import consumer
from openid.extensions import pape, sreg, ax

'''
' Function: Main
' Description: Completes OpenID authentication process and prints results
'''
def main():
    #create a base consumer object
    oidconsumer = consumer.Consumer({}, None)
    
    #create return to url
    url = "http://%s/complete.py" % (os.environ['HTTP_HOST'])
    
    #print page content type
    print 'Content-Type: text/plain'
    print ''
    
    #parse query string parameters into dictionary
    params = {}
    string_split = [s for s in os.environ['QUERY_STRING'].split('&') if s]
    for item in string_split:
        key,value = item.split('=')
        params[key] = urllib.unquote(value)
    
    #complete OpenID authentication and get identifier
    info = oidconsumer.complete(params, url)
    display_identifier = info.getDisplayIdentifier()
    
    #get simple registration and pape extension responses
    sreg_resp = sreg.SRegResponse.fromSuccessResponse(info)
    pape_resp = pape.Response.fromSuccessResponse(info)
    
    #build attribute exchange response object
    ax_response = ax.FetchResponse.fromSuccessResponse(info)
    if ax_response:
        ax_items = {
            'email': ax_response.get('http://axschema.org/contact/email'),
            'fullname': ax_response.get('http://axschema.org/namePerson'),
            'gender': ax_response.get('http://axschema.org/person/gender'),
            'picture': ax_response.get('http://axschema.org/media/image/default')
        }
        
        print ax_items
    
    #print all OpenID responses 
    print display_identifier
    
    if sreg_resp is not None:
        print sreg_resp
    
    if pape_resp is not None:
        print pape_resp
    
    if info.status == consumer.FAILURE and display_identifier:
        message = "Verification failed"
    elif info.status == consumer.SUCCESS:
        message = 'Success'
    elif info.status == consumer.CANCEL:
        message = 'Verification cancelled'
    elif info.status == consumer.SETUP_NEEDED:
        message = 'Setup needed'
    else:
        message = 'Verification failed.'
    print message

if __name__ == '__main__':
    main()