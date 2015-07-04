#!/usr/bin/python
import transmissionrpc
import sys
user='admin'
password='admin'
host = sys.argv[1]
port = sys.argv[2]
mode = sys.argv[3]
path = sys.argv[4]
if len(sys.argv)>5:
	user = sys.argv[5]
	password = sys.argv[6]
tc = transmissionrpc.Client(host, port=port, user=user, password=password, path=path)
list=tc.get_torrents()
listid=[]
for x in list:
	listid.append(x.id)
if mode == 'pause':
	for item in listid:
		tc.stop_torrent(item)
else:
	for item in listid:
		tc.start_torrent(item)
