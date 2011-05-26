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
' Class: Publishing Error
' Description: Custom error class for publishing exceptions
'''
class PublishError(Exception):
    def __init__(self, value):
        self.value = value
    def __str__(self):
        return repr(self.value)

'''
' Class: Publisher
' Description: Provides ability to publish updates for feeds
'''
class Publisher:
    regex_url = re.compile('^https?://')    #simple URL string validator
    
    #constructor that stores the hub for the publisher
    def __init__(self, hub):
        if self.regex_url.match(hub): self.hub = hub
        else: raise PublishError('Invalid hub URL supplied')
    
    #makes request to hub to update feeds
    def publish(self, feeds):
        #set the POST string mode
        post_string = 'hub.mode=publish'
        
        #add each feed as a URL in the POST string, unless invalid URL
        for feed in feeds:
            if self.regex_url.match(feed):
                post_string += '&hub.url=%s' % (urllib.quote(feed))
            else:
                raise PublishError('Invalid feed URL supplied: %s' % (feed))
            
        try:
            #make request to hub
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
            raise PublishError('%s, Response: "%s"' % (e, error))    