#!/bin/env python
# -*- coding: utf-8 -*-
import traceback
import commands
import urllib
import time
import simplejson
import socket
import struct
import os
import sys

vms = {}

########################################################################
def init():
	global vms
	
	(status, output) = commands.getstatusoutput('/usr/bin/xenstore-list /local/domain')
	if status != 0:
		return
	domids = output.split()
	for domid in domids:
		cmd = "/usr/bin/xenstore-read /local/domain/%s/name"%(domid)
		(status, output) = commands.getstatusoutput(cmd)
		if status != 0:
			continue
		vm = output.strip("\n")
		if vm.find('-509-') <= 0:
			continue
			
		cmd = "/usr/bin/xenstore-read /local/domain/%s/vm"%(domid)
		(status, output) = commands.getstatusoutput(cmd)
		if status != 0:
			continue
		uuid = output.strip("\n")
		if len(uuid) < 36:
			continue
		uuid = uuid[-36:]
			
		cmd = "/usr/bin/xenstore-read /local/domain/%s/console/tty"%(domid)
		(status, output) = commands.getstatusoutput(cmd)
		if status != 0:
			continue
		tty = output.strip("\n")
		
		vms[vm] = {}
		vms[vm]['tty'] = tty
		vms[vm]['uuid'] = uuid
		#print vms

########################################################################
def get_task():
  global vms
  
  for vm in vms.keys():
    #print vm
    params = urllib.urlencode({'Opt':'Get', 'VMCode':vm})
    try:
      f = urllib.urlopen("http://xen-http-proxy:8000/ecloud_admin/VMNetworkConfigTask.php?%s"%(params))
      ret = f.read()
      try:
        ret = simplejson.loads(ret)
      except:
      	continue
      if ret['ret'] != 0:
      	continue
      network_config = simplejson.loads(ret['result'])
      state = do_vm_network_config(vm, network_config)
      set_task_feedback(vm, network_config, state)
    except:
      traceback.print_exc()
      continue

########################################################################
def do_vm_network_config(vm, network_config):
  global vms
  #print network_config
  #{'private_gateway': '10.11.0.1', 'public_mask': '27', 'public_ip': '121.201.55.33', 
  #'public_gateway': '121.201.55.33', 'private_ip': '10.11.0.3', 'taskid': '4', 
  #'private_vlan': 3, 'private_mask': '24', 'vm_code': 'A-08-509-26-20150113-006', 'public_vlan': 3}
  
  print time.strftime('%Y-%m-%d %X',time.localtime(time.time()))
  print("vm %s network_config"%(vm))
  print network_config
  
  if vm_network_config_1(vm, network_config) == True:
    return vm_network_config_2(vm, network_config)
  else:
    return False

########################################################################
def vm_network_config_1(vm, network_config):
  global vms
  
  private_vlan_name = "network.eth0.vlan.%03d"%(int(network_config['private_vlan']))
  public_vlan_name = "network.eth1.vlan.%03d"%(int(network_config['public_vlan']))
	
  private_network_uuid = get_network_uuid(private_vlan_name)
  public_network_uuid = get_network_uuid(public_vlan_name)
  
  print time.strftime('%Y-%m-%d %X',time.localtime(time.time()))
  print("%s private[%s %s] public[%s %s]" \
       %(vm, private_vlan_name, private_network_uuid, public_vlan_name, public_network_uuid))
  
  if private_network_uuid == False or public_network_uuid == False:
    print("get network uuid fail %s %s"%(private_network_uuid, public_network_uuid))
    return False

  print("create vm vif %s %s %s"%(vm, private_network_uuid, public_network_uuid))
  private_vif_uuid = create_vm_vif(vms[vm]['uuid'], 0, private_network_uuid)
  public_vif_uuid = create_vm_vif(vms[vm]['uuid'], 1, public_network_uuid)
  if private_vif_uuid == False or public_vif_uuid == False:
  	print("create vif fail %s %s %s"%(vm, private_network_uuid, public_network_uuid))
  	return False
  
  print("vm vif plug %s %s %s"%(vm, private_vif_uuid, public_vif_uuid))
  if vm_vif_plug(vms[vm]['uuid'], private_vif_uuid) == False or \
     vm_vif_plug(vms[vm]['uuid'], public_vif_uuid) == False:
    print("vm vif plug fail %s %s %s"%(vm, private_vif_uuid, public_vif_uuid))
    return False

  return True

########################################################################
def get_network_uuid(name):
	cmd = "xe network-list params=uuid name-label=%s"%(name)
	(status, output) = commands.getstatusoutput(cmd)
	if status != 0:
		return False
	x = output.split(':')
	if len(x) != 2:
		return False
	return x[1].strip()

########################################################################
def create_vm_vif(vm_uuid, device_idx, network_uuid):
	cmd = "xe vif-create vm-uuid=%s device=%d network-uuid=%s"%(vm_uuid, device_idx, network_uuid)
	(status, output) = commands.getstatusoutput(cmd)
	if status != 0:
		return False
	if len(output) != 36:
		return False
	return output

########################################################################
def vm_vif_plug(vm_uuid, vif_uuid):
  cmd = "xe vif-plug vm-uuid=%s uuid=%s"%(vm_uuid, vif_uuid)
  (status, output) = commands.getstatusoutput(cmd)
  if status != 0:
    return False
  return True

########################################################################  
def vm_network_config_2(vm, network_config):  
  global vms
  
  #we must login first
  do_vm_tty_shell(vm, 'root')
  time.sleep(1)
  do_vm_tty_shell(vm, 'Rjkj@efly#123')
  
  private_mask = calc_netmask(int(network_config['private_mask']))
  cmd = "/var/opt/ECloud-VM-Utils/network_config.py " + \
        "opt=set_interface interface=%s BOOTPROTO=static IPADDR=%s GATEWAY=%s NETMASK=%s" \
        %('eth0', network_config['private_ip'], network_config['private_gateway'], private_mask)
  do_vm_tty_shell(vm, cmd)
  
  public_mask = calc_netmask(int(network_config['public_mask']))
  cmd = "/var/opt/ECloud-VM-Utils/network_config.py " + \
        "opt=set_interface interface=%s BOOTPROTO=static IPADDR=%s GATEWAY=%s NETMASK=%s" \
        %('eth1', network_config['public_ip'], network_config['public_gateway'], public_mask)
  do_vm_tty_shell(vm, cmd)
  
  return True

########################################################################
def calc_netmask(netmask):
  netmask = 1 << (32 - netmask)
  netmask = netmask - 1
  netmask = 0xFFFFFFFF ^ netmask
  netmask = socket.inet_ntoa(struct.pack('I',socket.htonl(netmask)))
  return netmask	

########################################################################
def do_vm_tty_shell(vm, cmd):
  global vms
  
  vmtty = vms[vm]['tty']
  print time.strftime('%Y-%m-%d %X',time.localtime(time.time()))
  print cmd
  fd = os.open(vmtty, os.O_RDWR)
  os.write(fd, cmd+"\n")
  #ret = os.read(fd, 1024)
  os.close(fd)
  return True

########################################################################
def set_task_feedback(vm, network_config, state):
	
  taskid = int(network_config['taskid'])
  params = urllib.urlencode({'Opt':'Set', 'VMCode':vm, 'TaskID':taskid,\
                             'State':'finish', 'Ret':int(not state), 'Result':'', 'Error':''})
  try:
    f = urllib.urlopen("http://xen-http-proxy:8000/ecloud_admin/VMNetworkConfigTask.php?%s"%(params))
    print f.read()
  except:
    traceback.print_exc()

########################################################################
def do_main():
  global vms
	
  init()
  get_task()

########################################################################
if __name__ == "__main__":
  try:
    do_main()
  except:
    traceback.print_exc()

