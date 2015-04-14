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
			f = urllib.urlopen("http://xen-http-proxy:8000/ecloud_admin/VMChangePassWordTask.php?%s"%(params))
			ret = f.read()
			try:
				ret = simplejson.loads(ret)
			except:
				continue
			if ret['ret'] != 0:
				continue
			task_data = simplejson.loads(ret['result'])
			state = do_vm_chpasswd(vm, task_data)
			set_task_feedback(vm, task_data, state)
		except:
			traceback.print_exc()
		continue

########################################################################  
def do_vm_chpasswd(vm, task_data):  
	global vms
  
	cmd = "echo \"root:%s\"|chpasswd"%(task_data['passwd'])
	do_vm_tty_shell(vm, cmd)
	do_vm_tty_shell(vm, 'history -c')
	do_vm_tty_shell(vm, 'clear')
	do_vm_tty_shell(vm, 'logout')

	return True

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
def set_task_feedback(vm, task_data, state):
	
	taskid = int(task_data['taskid'])
	params = urllib.urlencode({'Opt':'Set', 'VMCode':vm, 'TaskID':taskid,\
                             'State':'finish', 'Ret':int(not state), 'Result':'', 'Error':''})
	try:
		f = urllib.urlopen("http://xen-http-proxy:8000/ecloud_admin/VMChangePassWordTask.php?%s"%(params))
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


