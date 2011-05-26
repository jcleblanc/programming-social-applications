from OpenGraph import OpenGraphParser

#initialize open graph parser class instance with url
url = 'http://www.nhl.com/ice/player.htm?id=8468482';
og_instance = OpenGraphParser(url)

#output since description and entire og tag dictionary
print og_instance.get_one('description')
print og_instance.get_all()