import sys
import cgi
import openid
import os

from openid.consumer import consumer
from openid.extensions import pape, sreg, ax

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
                #build trust root and url to return to
                trust_root = 'http://%s/' % (os.environ['HTTP_HOST'])
                return_to = 'http://%s/complete.py' % (os.environ['HTTP_HOST'])
                
                #simple registration extension request
                sreg_request = sreg.SRegRequest(required=['nickname'], optional=['fullname', 'email'])
                request.addExtension(sreg_request)
                
                #attribute exchange extension request
                ax_request = ax.FetchRequest()
                ax_request.add(ax.AttrInfo('http://axschema.org/contact/email', required=False, alias='email'))
                ax_request.add(ax.AttrInfo('http://axschema.org/namePerson', required=False, alias='fullname'))
                ax_request.add(ax.AttrInfo('http://axschema.org/person/gender', required=False, alias='gender'))
                ax_request.add(ax.AttrInfo('http://axschema.org/media/image/default', required=False, alias='picture'))
                request.addExtension(ax_request)
                
                #pape policy extension request
                if params.has_key('policy_phishing'):
                    pape_request = pape.Request([pape.AUTH_PHISHING_RESISTANT])
                    request.addExtension(pape_request)
                
                #openid v1 - send through redirect
                if request.shouldSendRedirect():
                    redirect_url = request.redirectURL(
                        trust_root, return_to, immediate='immediate')
                    print "Location: " + redirect_url
                #openid v2 - use javascript form to send POST to server
                else:
                    form_html = request.htmlMarkup(
                        trust_root, return_to,
                        form_tag_attrs={'id':'openid_message'})

                    print_msg(form_html, 'text/html')

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
    