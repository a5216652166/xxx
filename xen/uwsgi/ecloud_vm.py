import sys
import time
import json
import XenAPI
import traceback

args = pool_data = {}
session = ''

def _get_vm_power_state():
	global args, pool_data, session

	ret = {}
	vm_name_label = args['vm_name_label']
	vm = session.xenapi.VM.get_by_name_label(vm_name_label)
	record = session.xenapi.VM.get_record(vm[0])
	ret['vm_name_label'] = vm_name_label
	ret['power_state'] = record['power_state']
	return json.dumps(ret)

def _set_vm_power_state():
	global args, pool_data, session

	ret = {}
	vm_name_label = args['vm_name_label']
	power_state = args['power_state']
	vm = session.xenapi.VM.get_by_name_label(vm_name_label)
	vm = vm[0]
	#### Halt|Pause|Run|Suspend|Reboot|Resume ####
	####
	if power_state == 'Halt':
		session.xenapi.Async.VM.shutdown(vm)
	elif power_state == 'Pause':
		session.xenapi.Async.VM.pause(vm)
	elif power_state == 'Run':
		session.xenapi.Async.VM.start(vm, False, True) # start_paused = False; force = True
	elif power_state == 'Suspend':
		session.xenapi.Async.VM.suspend(vm)
	elif power_state == 'Reboot':
		session.xenapi.Async.VM.hard_reboot(vm)
	elif power_state == 'Resume':
		session.xenapi.Async.VM.resume(vm, False, True) # start_paused = False; force = True
	elif power_state == 'Unpause':
		session.xenapi.Async.VM.unpause(vm)
	else:
		pass
	####
	ret['vm_name_label'] = vm_name_label
	return json.dumps(ret)

def _do_main(query):
	global args, session
	#
	#{u'opt': u'get_host_list', 
	# u'pool_data': {u'uuid': u'7106005d-e8e3-4da5-8adc-4e8035da77ad', u'master': u'10.11.253.43', u'user': u'root', u'pass': u'Rjkj@efly#123'}, 
	#u'module': u'ecloud_pool'}
	#
	
	args = json.loads(query)
	pool_data = args['pool_data']
	
	master_url = "http://%s/"%(pool_data['master'])
	session = XenAPI.Session(master_url)
	session.xenapi.login_with_password(pool_data['user'], pool_data['pass'])

	if args['opt'] == 'get_vm_power_state':
		return _get_vm_power_state()
	elif args['opt'] == 'set_vm_power_state':
		return _set_vm_power_state()
	else:
		return 'unknow opt'

	session.xenapi.session.logout()

def invoke(query):
	try:
		return _do_main(query)
	except:
		traceback.print_exc()

