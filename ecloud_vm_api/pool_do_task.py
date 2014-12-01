#!/bin/env python
import urllib
import urllib2
import json

url = 'http://61.142.208.98/ecloud_vm_api/webserver/task.php'

post_data = { 'opt':'get', 'totype':'pool', 'todo':'XEN_TEST1' }

post_data = 'data=' + json.dumps(post_data)

req = urllib2.Request(url, post_data)

response = urllib2.urlopen(req)

ret = response.read()

task_data = json.loads(ret)

print task_data




