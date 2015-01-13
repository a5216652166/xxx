#!/bin/env python
import traceback
import commands
import urllib
import simplejson

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
		print vms

########################################################################
def get_task():
  global vms
  
  for vm in vms.keys():
    print vm
    params = urllib.urlencode({'Opt':'Get', 'VMCode':vm})
    try:
      f = urllib.urlopen("http://xen-http-proxy:8000/ecloud_admin/VMNetworkConfigTask.php?%s"%(params))
      ret = f.read()
      ret = simplejson.loads(ret)
      if ret['ret'] != 0:
      	continue
      network_config = simplejson.loads(ret['result'])
      do_vm_network_config(vm, network_config)
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
  
	if vm_network_config_1(vm, network_config) == True:
	  vm_network_config_2(vm, network_config)

########################################################################
def vm_network_config_1():
  global vms
  pass
  
def vm_network_config_2():  
  global vms
  pass

########################################################################
def get_vlan_network(public_vlan, private_vlan):
	pass
	
########################################################################
def create_vm_vif(vm, public_network, private_network):
	pass	

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

