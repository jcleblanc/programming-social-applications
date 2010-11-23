from OpenGraph import OpenGraphParser

url = 'http://www.nhl.com/ice/player.htm?id=8468482';
og_instance = OpenGraphParser(url)

print og_instance.get_one('description')
print og_instance.get_all()