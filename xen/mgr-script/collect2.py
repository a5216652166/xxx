#!/usr/bin/env python
#http://www.openfoundry.org/of/download/cloudsecurity1/0.0.1b/collect2.py

import time
import XenAPI
import parse_rrd
import sys

master = '10.11.253.43'
slaves = ['10.11.253.44', '10.11.253.45']
user='root'
passwd='Rjkj@efly#123'
session = None

def print_latest_host_data(rrd_updates, sx):
    host_uuid = rrd_updates.get_host_uuid()
    for host in sx.host.get_all():
        print "Name:"+sx.host.get_name_label(host)
    print "Host:"+host_uuid
    nt = time.strftime("%H:%M:%S", time.localtime(time.time()))
    Out='Time: '
    print Out+nt
    for param in rrd_updates.get_host_param_list():
        if param != "":
            max_time=0
            data=""
            for row in range(rrd_updates.get_nrows()):
                 epoch = rrd_updates.get_row_time(row)
                 dv = str(rrd_updates.get_host_data(param,row))
                 if epoch > max_time:                
                     max_time = epoch
                     data=dv
            
            print param+" "+data


def print_latest_vm_data(rrd_updates, uuid):
    print "VM:"+uuid
    nt = time.strftime("%H:%M:%S", time.localtime(time.time()))
    Out = 'Time: '
    print Out+nt
    for param in rrd_updates.get_vm_param_list(uuid):
        if param != "":
            max_time=0
            data=""
            for row in range(rrd_updates.get_nrows()):
                epoch = rrd_updates.get_row_time(row)
                dv = str(rrd_updates.get_vm_data(uuid,param,row))
                if epoch > max_time:
                    max_time = epoch
                    data = dv
            
            print param+" "+data

def build_vm_graph_data(rrd_updates, vm_uuid, param):
    time_now = int(time.time())
    for param_name in rrd_updates.get_vm_param_list(vm_uuid):
        if param_name == param:
            data = "#%s  Seconds Ago" % param
            for row in range(rrd_updates.get_nrows()):                
                epoch = rrd_updates.get_row_time(row)
                data += str(rrd_updates.get_vm_data(vm_uuid, param_name, row))
                data += "\n%-14s %s" % (data, time_now - epoch)
            return data

def print_metrics(sx, uuid):
    for host in sx.host.get_all():
        for vm in sx.host.get_resident_VMs(host): 
            if uuid == sx.VM.get_uuid(vm):
                print '--------------------------------------'
                print "Name:"+sx.VM.get_name_label(vm)
                break

def build_start_time():
    tt = time.localtime(time.time())
    mmin = int(tt.tm_min / 5) * 5
    ss = "%d-%02d-%02d %02d:%02d:%02d"%(tt.tm_year, tt.tm_mon, tt.tm_mday, tt.tm_hour, mmin, 0)
    ts = time.strptime(ss, "%Y-%m-%d %H:%M:%S")
    start_time = int(time.mktime(ts))
    return start_time

def init_session():
    global session

    url = "http://%s"%(master)
    session = XenAPI.Session(url)
    session.xenapi.login_with_password(user, passwd)

def get_server_rrd_updates(server):
    url = "http://%s"%(server)
    rrd_updates = parse_rrd.RRDUpdates()
    params = {}
    #params['cf'] = "MIN"
    params['start'] = build_start_time()
    #params['interval'] = 5
    #params['host'] = "true"
    rrd_updates.refresh(session.handle, params, url)
    
    if 'host' in params and params['host'] == 'true':
        print_latest_host_data(rrd_updates, session.xenapi)
    #return
    for uuid in rrd_updates.get_vm_list():
        print_metrics(session.xenapi, uuid)
        print_latest_vm_data(rrd_updates, uuid)
        #param = 'cpu0'
        #data = build_vm_graph_data(rrd_updates, uuid, param)
        #fh = open("%s-%s.dat" % (uuid, param), 'w')
        #fh.write(data)
        #fh.close()

def main():
    init_session()
    get_server_rrd_updates(master)
    for slave in slaves:
        print slave
        get_server_rrd_updates(slave)

#while True:
main()
 # time.sleep(5)

#xe host-param-set uuid=3edca602-b63d-4d86-bf3a-ca3d484f7bc7 other-config:rrd_update_interval=1
