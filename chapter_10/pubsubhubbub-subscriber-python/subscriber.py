#!/usr/bin/env python
#
# Copyright 2011 Jonathan LeBlanc
#
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
#
#     http://www.apache.org/licenses/LICENSE-2.0
#
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and
# limitations under the License.
#

import re
import urllib
import urllib2

'''
' Class: Subscription Error
' Description: Custom error class for subscription exceptions
'''
class SubscribeError(Exception):
    def __init__(self, value):
        self.value = value
    def __str__(self):
        return repr(self.value)

'''
' Class: Subscriber
' Description: Provides ability to subscribe / unsubscribe from hub feeds
'''
class Subscriber:
    regex_url = re.compile('^https?://')    #simple URL string validator
    
    #constructor that stores the hub and callback URLs for the subscriber
    def __init__(self, hub, callback):
        if self.regex_url.match(hub): self.hub = hub
        else: raise SubscribeError('Invalid hub URL supplied')
        
        if self.regex_url.match(callback): self.callback = callback
        else: raise SubscribeError('Invalid callback URL supplied')
    
    #initiates a request to subscribe to a feed
    def subscribe(self, feed):
        return self.change_subscription('subscribe', feed)
    
    #initiates a request to unsubscribe from a feed
    def unsubscribe(self, feed):
        return self.change_subscription('unsubscribe', feed)
    
    #makes request to hub to subscribe / unsubscribe
    def change_subscription(self, mode, feed):
        #check if provided feed is a valid URL
        if self.regex_url.match(feed): 
            #set the post string for subscribe / unsubscribe
            post_string = 'hub.mode=%s&hub.callback=%s&hub.verify=async&hub.topic=%s' % (mode, self.callback, urllib.quote(feed))
            
            try:
                #make return to hub
                file = urllib2.urlopen(self.hub, post_string)
                return True
            except (IOError, urllib2.HTTPError), e:
                #process http conditions in 2xx range as valid
                if hasattr(e, 'code') and str(e.code)[0] == '2':
                    return True
                
                #process alternative error conditions
                error = ''
                if hasattr(e, 'read'):
                    error = e.read()
                raise SubscribeError('%s, Response: "%s"' % (e, error))
        else:
            raise SubscribeError('Invalid feed URL supplied')