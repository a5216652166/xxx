#!/usr/bin/env python
#http://www.openfoundry.org/of/download/cloudsecurity1/0.0.1b/collect2.py

import time
import XenAPI
import parse_rrd
import sys
import traceback
import urllib
import urllib2
import json

#master = '10.11.253.43'
#slaves = ['10.11.253.44', '10.11.253.45']
#user='root'
#passwd='Rjkj@efly#123'
session = None
pool_code = None
pool_data = {}
hosts = []
start_time = 0

def get_pool_data(pool_code):
    global session
    global pool_data
    global hosts

    try:
        params = urllib.urlencode({'PoolCode':pool_code, 'Opt':'ConfigData'})
        f = urllib.urlopen("http://api.efly.cc/ecloud_admin/GetPoolData.php?%s"%(params))
        ret = json.loads(f.read())
        if ret['ret'] != 0:
            return False
        pool_data = ret['result']
    except:
        traceback.print_exc()

    try:
        params = urllib.urlencode({'PoolCode':pool_code, 'Opt':'HostList'})
        f = urllib.urlopen("http://api.efly.cc/ecloud_admin/GetPoolData.php?%s"%(params))
        ret = json.loads(f.read())
        if ret['ret'] != 0:
            return False
        hosts = ret['result']
    except:
        traceback.print_exc()

    #print pool_data
    #print hosts

    master_url = "http://%s/"%(pool_data['master'])
    session = XenAPI.Session(master_url)
    session.xenapi.login_with_password(pool_data['user'], pool_data['pass'])

def print_latest_host_data(rrd_updates, sx, last_time):
    host_uuid = rrd_updates.get_host_uuid()
    host = sx.host.get_by_uuid(host_uuid)
    #print '-------------------------------------------------------------'
    host_name = sx.host.get_name_label(host)
    #print "[HostName: " + host_name + "]"
    post_data = {}
    post_data['name'] = host_name
    post_data['timestamp'] = last_time
    post_data['performance'] = []
    for param in rrd_updates.get_host_param_list():
        if param == "":
            continue
        total = rows = 0
        for row in range(rrd_updates.get_nrows()):
            epoch = rrd_updates.get_row_time(row)
            dv = str(rrd_updates.get_host_data(param,row))
            #print "row=%s epoch=%s dv=%s"%(row, epoch, dv)
            if epoch > last_time + 300:
                break
            total += float(dv)
            rows += 1
        post_data['performance'].append({param:total/rows})
    post_performance_data(host_name, post_data)

def print_latest_vm_data(rrd_updates, sx, uuid, last_time):
    vm = sx.VM.get_by_uuid(uuid)          
    vm_name = sx.VM.get_name_label(vm)
    if len(vm_name) != 24 or vm_name.find('-509-') <= 0:
        return
    #print '-------------------------------------------------------------'
    #print "[VMName:"+sx.VM.get_name_label(vm) + "]"
    post_data = {}
    post_data['name'] = vm_name
    post_data['timestamp'] = last_time
    post_data['performance'] = []
    for param in rrd_updates.get_vm_param_list(uuid):
        if param == "":
            continue
        total = rows = 0
        for row in range(rrd_updates.get_nrows()):
            epoch = rrd_updates.get_row_time(row)
            dv = str(rrd_updates.get_vm_data(uuid,param,row))
            if epoch > last_time + 300:
                break
            total += float(dv)
            rows += 1
        post_data['performance'].append({param:total/rows})
    post_performance_data(vm_name, post_data)

def post_performance_data(name, post_data):
    post_data = 'postdata=' + json.dumps(post_data)
    url = 'http://api.efly.cc/ecloud_admin/PostPerformanceData.php'
    req = urllib2.Request(url, post_data)
    f = urllib2.urlopen(req)
    print f.read()

def build_start_time():
    tt = time.localtime(time.time()-300)
    mmin = int(tt.tm_min / 5) * 5
    ss = "%d-%02d-%02d %02d:%02d:%02d"%(tt.tm_year, tt.tm_mon, tt.tm_mday, tt.tm_hour, mmin, 0)
    ts = time.strptime(ss, "%Y-%m-%d %H:%M:%S")
    #print("build_start_time %s"%(ts))
    print time.strftime("%Y-%m-%d %H:%M:%S", ts)
    start_time = int(time.mktime(ts))
    return start_time

def get_host_rrd_updates(server):
    global start_time

    url = "http://%s"%(server)
    rrd_updates = parse_rrd.RRDUpdates()
    params = {}
    params['cf'] = 'AVERAGE'
    params['start'] = start_time
    #params['interval'] = 5
    params['host'] = "true"
    rrd_updates.refresh(session.handle, params, url)
    
    if 'host' in params and params['host'] == 'true':
        print_latest_host_data(rrd_updates, session.xenapi, params['start'])

def get_vm_rrd_updates(server):
    global start_time

    url = "http://%s"%(server)
    rrd_updates = parse_rrd.RRDUpdates()
    params = {}
    params['cf'] = 'AVERAGE'
    params['start'] = start_time
    #params['interval'] = 5
    #params['host'] = "true"
    rrd_updates.refresh(session.handle, params, url)
    
    for vm_uuid in rrd_updates.get_vm_list():
        print_latest_vm_data(rrd_updates, session.xenapi, vm_uuid, params['start'])

def main():
    global pool_code
    global hosts
    global start_time

    pool_code = sys.argv[1]
    if get_pool_data(pool_code) == False:
        return

    start_time = build_start_time()
    #return

    for host in hosts:
        get_host_rrd_updates(host)
    for host in hosts:
        get_vm_rrd_updates(host)

if __name__ == "__main__":
    try:
        main()
    except:
        error = traceback.format_exc()
        print error

