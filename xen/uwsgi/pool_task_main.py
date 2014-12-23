#!/bin/env python
import sys
import time
import config
import XenAPI
import traceback
import provision
import json
import MySQLdb
import config

session = ''
task = ''

def _init_session():
	# {u'vm_code': u'A-08-509-26-20141211-016', 
	# 'pool_code': 'P-26-001', u'ram': u'2048', u'cpu': u'4', 
	# u'template_code': u'Template_CentOS_6.5_x86_64'}
	#
	global session, task

	pool_code = task['pool_code']

	conn = MySQLdb.connect(host=config.ecloud_db_ip, user=config.ecloud_db_user, \
							passwd=config.ecloud_db_pass, db='ecloud_admin', port=3306)
	cur = conn.cursor(cursorclass = MySQLdb.cursors.DictCursor)
	sql = "select * from `Pool` where `PoolCode` = '%s'"\
				" and `State` = 'enabled';"%(pool_code)
	cur.execute(sql)
	row = cur.fetchone()
	if row == None:
		return False
	pool_data = json.loads(row['Data'])
	#print pool_data
	# {u'uuid': u'7106005d-e8e3-4da5-8adc-4e8035da77ad', 
	# u'master': u'10.11.253.43', u'user': u'root', u'pass': u'Rjkj@efly#123'}

	master_url = "http://%s/"%(pool_data['master'])
	session = XenAPI.Session(master_url)
	session.xenapi.login_with_password(pool_data['user'], pool_data['pass'])
	return True

def _get_create_template_vm_task():
	global session, task

	conn = MySQLdb.connect(host=config.ecloud_db_ip, user=config.ecloud_db_user, \
							passwd=config.ecloud_db_pass, db='ecloud_admin', port=3306)
	cur = conn.cursor(cursorclass = MySQLdb.cursors.DictCursor)
	cur.execute("select * from `PoolTask` where `Type` = 'VMTemplateCreate'"\
				" and `State` = 'init' and `Tried` < `Try`;")
	row = cur.fetchone()
	if row == None:
		return False

	taskid = row['ID']
	task = json.loads(row['Data'])
	task['pool_code'] = row['PoolCode']
	task['taskid'] = taskid

	sql = "update `PoolTask` set `State` = 'doing', `Tried` = `Tried` + 1, "\
		"`StartTime` = now() where `ID` = %d;"%(taskid)
	print sql
	cur.execute(sql)

	return True

def _set_task_feedback(taskid, state, ret, result, error):
	global task 
	conn = MySQLdb.connect(host=config.ecloud_db_ip, user=config.ecloud_db_user, \
							passwd=config.ecloud_db_pass, db='ecloud_admin', port=3306)
	cur = conn.cursor(cursorclass = MySQLdb.cursors.DictCursor)
	sql = "update `PoolTask` set `State` = '%s', `Tried` = `Tried` + 1, "\
			"`FinishTime` = now(), `Ret` = %d, `Result` = '%s', `Error` = '%s' where `ID` = %d;"\
			%(state, ret, result, error, taskid)
	print sql
	cur.execute(sql)

	if ret != 0:
		return
	#set vm enabled
	cur = conn.cursor(cursorclass = MySQLdb.cursors.DictCursor)
	sql = "update `VM` set `State` = '%s', "\
		"`TimeStamp` = now() where `VMCode` = '%s';"\
		%('enabled', task['vm_code'])
	print sql
	cur.execute(sql)

def _vm_provision():
	# {u'vm_code': u'A-08-509-26-20141211-016', 
	# 'pool_code': 'P-26-001', u'ram': u'2048', u'cpu': u'4', 
	# u'template_code': u'Template_CentOS_6.5_x86_64'}
	#
	global session, task

	vm_code = task['vm_code']
	template = session.xenapi.VM.get_by_name_label(task['template_code'])[0]
	print "create %s %s => %s \n"%(template, task['template_code'], vm_code)

	vm = session.xenapi.VM.clone(template, vm_code)
	session.xenapi.VM.set_PV_args(vm, "noninteractive")
	session.xenapi.VM.provision(vm)

	return vm

def _vm_set_cpu(vm, cpu):
	global session

	session.xenapi.VM.set_VCPUs_max(vm, cpu)
	session.xenapi.VM.set_VCPUs_at_startup(vm, cpu)

def _vm_set_ram(vm, ram):
	global session

	record = session.xenapi.VM.get_record(vm)
	mem_dynamic_min = record['memory_dynamic_min'] 
	mem_dynamic_max = record['memory_dynamic_max']
	mem_static_min = record['memory_static_min']
	mem_static_max = record['memory_static_max']
	mem_dynamic_max = str(int(ram)*1024*1024)
	mem_static_max = str(int(ram)*1024*1024)	
	session.xenapi.VM.set_memory_limits(vm, mem_static_min, mem_static_max, mem_dynamic_min, mem_dynamic_max)

def _vm_start(vm):
	global session

	session.xenapi.VM.start(vm, False, True)

def _create_template_vm():
	global session, task

	vm = _vm_provision()
	if vm == None:
		return
	_vm_set_cpu(vm, task['cpu'])
	_vm_set_ram(vm, task['ram'])
	_vm_start(vm)

def _do_main():
	global session, task
	
	if _get_create_template_vm_task() == False:
		return
	print task

	if _init_session() == False:
		return

	_create_template_vm()

	_set_task_feedback(task['taskid'], 'finish', 0, '', '')

	session.xenapi.session.logout()

def read_os_name(vm):
	vgm = session.xenapi.VM.get_guest_metrics(vm)
	try:
		os = session.xenapi.VM_guest_metrics.get_os_version(vgm)
		if "name" in os.keys():
			return os["name"]
		return None
	except:
		return None

def read_ip_address(vm):
	vgm = session.xenapi.VM.get_guest_metrics(vm)
	try:
		os = session.xenapi.VM_guest_metrics.get_networks(vgm)
		if "0/ip" in os.keys():
			return os["0/ip"]
		return None
	except:
		return None


if __name__ == "__main__":
	try:
		_do_main()
	except:
		error = traceback.format_exc()
		print error

