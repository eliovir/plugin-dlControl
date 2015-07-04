#!/usr/bin/python
#inspired by martin and corrected by sarakha63
import httplib
import xml.etree.ElementTree as etree
import socket
import re
import sys
import time
lgtv = {}
dialogMsg =""
headers = {"Content-Type": "application/atom+xml"}
print sys.argv
lgtv["pairingKey"] = sys.argv[3]
def getip():
    ipaddress = sys.argv[2]
    return ipaddress
def displayKey():
    conn = httplib.HTTPConnection( lgtv["ipaddress"], port=8080)
    reqKey = "<!--?xml version=\"1.0\" encoding=\"utf-8\"?--><auth><type>AuthKeyReq</type></auth>"
    conn.request("POST", "/roap/api/auth", reqKey, headers=headers)
    httpResponse = conn.getresponse()
    if httpResponse.reason != "OK" : sys.exit("Network error")
    return httpResponse.reason
def getSessionid():
    conn = httplib.HTTPConnection( lgtv["ipaddress"], port=8080)
    pairCmd = "<!--?xml version=\"1.0\" encoding=\"utf-8\"?--><auth><type>AuthReq</type><value>" \
            + lgtv["pairingKey"] + "</value></auth>"
    conn.request("POST", "/roap/api/auth", pairCmd, headers=headers)
    httpResponse = conn.getresponse()
    if httpResponse.reason != "OK" : return httpResponse.reason
    tree = etree.XML(httpResponse.read())
    return tree.find('session').text
def getPairingKey():
    displayKey()
def handleCommand(cmdcode):
    conn = httplib.HTTPConnection( lgtv["ipaddress"], port=8080)
    conn = httplib.HTTPConnection( lgtv["ipaddress"], port=8080)
    cmdText = "<?xml version=\"1.0\" encoding=\"utf-8\"?><command>" \
    +"<name>HandleKeyInput</name><value>" \
    +cmdcode \
    +"</value></command>"
    conn.request("POST", "/roap/api/command", cmdText, headers=headers)
    httpResponse = conn.getresponse()
    return 0
lgtv["ipaddress"] = getip()
theSessionid = getSessionid()
if theSessionid == "Unauthorized" :
    getPairingKey()
    time.sleep(20)
else:
    theSessionid = getSessionid()
if len(theSessionid) < 8 : sys.exit("Could not get Session Id: " + theSessionid)
lgtv["session"] = theSessionid
result = str(sys.argv[1])
handleCommand(result)
