import sys
import time
import json
import XenAPI
import traceback

args = pool_data = {}
session = ''

def _get_host_list():
	global args, pool_data, session

	ret = []
	hosts = session.xenapi.host.get_all()
	for host in hosts:
		record = session.xenapi.host.get_record(host)
		ret.append({'uuid':record['uuid'], 'name_label':record['name_label'], 'name_desc':record['name_description']})
	return json.dumps(ret)

def _get_vm_list():
	global args, pool_data, session

	ret = []
	vms = session.xenapi.VM.get_all()
	for vm in vms:
		record = session.xenapi.VM.get_record(vm)
		if record["is_a_template"] or record["is_control_domain"]:
			continue
		#print record['name_label'], record["is_a_template"], record["is_control_domain"]
		ret.append({'uuid':record['uuid'], 'name_label':record['name_label'], 'power_state':record['power_state']})
	return json.dumps(ret)

def _get_host_vm_list():
	global args, pool_data, session

	ret = []
	host_name_label = args['host_name_label']
	vms = session.xenapi.VM.get_all()
	for vm in vms:
		record = session.xenapi.VM.get_record(vm)
		if record["is_a_template"] or record["is_control_domain"]:
			continue
		host = record['resident_on']
		if host == 'OpaqueRef:NULL':
			continue
		if session.xenapi.host.get_name_label(host) == host_name_label:
			ret.append({'uuid':record['uuid'], 'name_label':record['name_label'], 'power_state':record['power_state']})
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
	
	rpc_url = pool_data['xen-api-rpc']
	session = XenAPI.Session(rpc_url)
	session.xenapi.login_with_password(pool_data['user'], pool_data['pass'])

	if args['opt'] == 'get_host_list':
		return _get_host_list()
	elif args['opt'] == 'get_vm_list':
		return _get_vm_list()
	elif args['opt'] == 'get_host_vm_list':
		return _get_host_vm_list()
	else:
		return 'unknow opt'

	session.xenapi.session.logout()

def invoke(query):
	try:
		return _do_main(query)
	except:
		traceback.print_exc()

