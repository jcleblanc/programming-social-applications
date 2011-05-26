import cgi
import openid

import common

from openid.consumer import consumer
from openid.extensions import ax

'''
' Function: Main
' Description: Initiates the OpenID authentication process
'''
def main():
    #get query parameters
    params = cgi.FieldStorage()
    
    #check if an OpenID url was specified
    if not params.has_key('openid_url'):
        print_msg('Please enter an OpenID Identifier to verify.', 'text/plain')
    else:
        #capture OpenID url 
        openid_url = params['openid_url'].value
        
        #create a base consumer object
        oidconsumer = consumer.Consumer({}, None)
        
        try:
            request = oidconsumer.begin(openid_url)
        except:
            print_msg('Error in discovery: ' + openid_url, 'text/plain')
        else:
            if request is None:
                print_msg('No OpenID services found', 'text/plain')
            else:
                #attribute exchange extension request
                ax_request = ax.FetchRequest()
                ax_request.add(ax.AttrInfo('http://axschema.org/contact/email', required=False, alias='email'))
                ax_request.add(ax.AttrInfo('http://axschema.org/media/image/default', required=False, alias='picture'))
                request.addExtension(ax_request)
                
                #add oauth hybrid extension parameters to redirect url
                redirect_url = request.redirectURL(common.trust_root, common.return_to)
                redirect_url += '&openid.ns.oauth=http%3A%2F%2Fspecs.openid.net%2Fextensions%2Foauth%2F1.0&openid.oauth.consumer=' + common.consumer_key
                
                #print_msg(redirect_url, 'text/plain')
                print "Location: " + redirect_url
                
'''
' Function: Print Message
' Description: Print a message with a provided content type
' Inputs: msg (string) - The message to be displayed
'         type (string) - The content type to use (e.g. text/plain)
'''
def print_msg(msg, type):
    if msg is not None:
        print 'Content-Type: %s' % (type)
        print ''
        print msg

#initiate load of main()
if __name__ == '__main__':
    main()
    