import urllib
import re
from BeautifulSoup import BeautifulSoup

"""
" Class: Open Graph Parser
" Description: Parses an HTML document to retrieve and store Open Graph
"              tags from the meta data
" Author: Jonathan LeBlanc
" License: Creative Commons (http://creativecommons.org/licenses/by-sa/2.0/)
" Useage:
"    url = 'http://www.nhl.com/ice/player.htm?id=8468482';
"    og_instance = OpenGraphParser(url)  
"    print og_instance.get_one('og:title')
"    print og_instance.get_all()
"""
class OpenGraphParser:
    og_content = {}
    
    """
    " Method: Init
    " Description: Initializes the open graph fetch.  If url was provided,
    "              og_content will be set to return value of get_graph method
    " Arguments: url (string) - The URL from which to collect the OG data
    """
    def __init__(self, url):
        if url is not None:
            self.og_content = self.get_graph(url)
    
    """
    " Method: Get Open Graph
    " Description: Fetches HTML from provided url then filters to only meta tags.
    "              Goes through all meta tags and any starting with og: get
    "              stored and returned to the init method.
    " Arguments: url (string) - The URL from which to collect the OG data
    " Returns: dictionary - The matching OG tags
    """
    def get_graph(self, url):
        #fetch all meta tags from the source of the url
        sock = urllib.urlopen(url) 
        htmlSource = sock.read()                            
        sock.close()                                        
        soup = BeautifulSoup(htmlSource)
        meta = soup.findAll('meta')
        
        #get all og:* tags from meta data
        content = {}
        for tag in meta:
            if tag.has_key('property'):
                if re.search('og:', tag['property']) is not None:
                    content[re.sub('og:', '', tag['property'])] = tag['content']
                    
        return content
    
    """
    " Method: Get One Tag
    " Description: Returns the content of one OG tag
    " Arguments: tag (string) - The OG tag whose content should be returned
    " Returns: string - the value of the OG tag
    """
    def get_one(self, tag):
        return self.og_content[tag]
    
    """
    " Method: Get All Tags
    " Description: Returns all found OG tags
    " Returns: dictionary - All OG tags
    """  
    def get_all(self):
        return self.og_content
