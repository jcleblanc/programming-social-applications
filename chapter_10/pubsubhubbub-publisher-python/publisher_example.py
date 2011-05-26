from publisher import *

#define hub and feeds
hub = 'http://pubsubhubbub.appspot.com/'
feeds = ['http://www.example.com/feed1.xml', 'http://www.example.com/feed2.xml', 'http://www.example.com/feed3.xml']

#create new publisher
publisher = Publisher(hub)

#publish feed updates: response == True on success
response = publisher.publish(feeds)

#print message on success
if (response == True):
    print 'Content-Type: text/plain'
    print ''
    print 'Update successful'