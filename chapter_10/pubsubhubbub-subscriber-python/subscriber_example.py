from subscriber import *

#define hub, callback and feed
hub = 'http://pubsubhubbub.appspot.com/'
callback = 'http://www.example.com/publish'
feed = 'http://www.example.com'

#create new subscriber
subscriber = Subscriber(hub, callback)

#subscribe / unsubscribe methods: response == True on success
response = subscriber.subscribe(feed)
#response = subscriber.unsubscribe(feed)

#print message on success
if (response == True):
    print 'Content-Type: text/plain'
    print ''
    print 'Request successful'