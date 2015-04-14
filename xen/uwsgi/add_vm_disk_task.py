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
	print sql
	cur.execute(sql)
	row = cur.fetchone()
	if row == None:
		return False
	print row['Data']
	pool_data = json.loads(row['Data'])
	#print pool_data
	# {u'uuid': u'7106005d-e8e3-4da5-8adc-4e8035da77ad', 
	# u'master': u'10.11.253.43', u'user': u'root', u'pass': u'Rjkj@efly#123'}

	#master_url = "http://%s/"%(pool_data['master'])
	rpc_url = pool_data['xen-api-rpc']
	session = XenAPI.Session(rpc_url)
	session.xenapi.login_with_password(pool_data['user'], pool_data['pass'])
	return True

def _get_add_vm_disk_task():
	global session, task

	conn = MySQLdb.connect(host=config.ecloud_db_ip, user=config.ecloud_db_user, \
							passwd=config.ecloud_db_pass, db='ecloud_admin', port=3306)
	cur = conn.cursor(cursorclass = MySQLdb.cursors.DictCursor)
	cur.execute("select * from `PoolTask` where `Type` = 'AddVMDisk'"\
				" and `State` = 'init' and `Tried` < `Try`;")
	row = cur.fetchone()
	if row == None:
		return False

	taskid = row['ID']
	task = json.loads(row['Data'])
	task['pool_code'] = row['PoolCode']
	task['taskid'] = taskid

	#check the vm if enabled and running now ?
	cur.execute("select * from `VM` where `VMCode` = '%s' and `State` = 'enabled'"%(task['vm_code']))
	row = cur.fetchone()
	if row == None:
		return False

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
	sql = "update `PoolTask` set `State` = '%s', "\
			"`FinishTime` = now(), `Ret` = %d, `Result` = '%s', `Error` = '%s' where `ID` = %d;"\
			%(state, ret, result, error, taskid)
	print sql
	cur.execute(sql)

	if ret != 0:
		return
	#set vm disk enabled
	cur = conn.cursor(cursorclass = MySQLdb.cursors.DictCursor)
	sql = "update `VMDisk` set `State` = '%s' "\
		" where `ID` = %d;"\
		%('enabled', task['vmdisk_id'])
	print sql
	cur.execute(sql)

def _add_vm_disk():
	global session, task

	sr_uuid = session.xenapi.SR.get_by_name_label(task['storage_name'])
	vm_ref = session.xenapi.VM.get_by_name_label(task['vm_code'])

	virtual_size = int(task['disk_size']) * 1024 * 1024 * 1024
	disk_name_label = "%s_%s_%sGB"%(task['vm_code'], task['disk_type'], task['disk_size'])

	vdi_rec = { 'name_label' : disk_name_label,
				'SR' : sr_uuid[0],
				'virtual_size': str(virtual_size),
				'sector_size' : 512,
				'type' : 'user',
				'sharable' : False,
				'read_only' : False,
				'other_config': {}
			}
	#print vdi_rec
	vdi_ref = session.xenapi.VDI.create(vdi_rec)
	print 'vid => ' + vdi_ref

	vbd_rec = { 'VM':vm_ref[0],
	'VDI':vdi_ref,
	'userdevice':'autodetect', 
	'bootable':False,
	#'device':"xvda1",
	'mode':'RW',
	'type':'Disk',
	'empty':False,
	'other_config':{},
	'qos_algorithm_type':'',
	'qos_algorithm_params':{},
	'qos_supported_algorithms':[],
	#'driver':1,
	}
	vbd_ref = session.xenapi.VBD.create(vbd_rec)
	print 'vbd => ' + vbd_ref	

	vbds = session.xenapi.VBD.get_all_records_where( "field \"VDI\" = \"%s\"" % vdi_ref[0])
	print vbds
	for vbd_ref, vbd_info in vbds.items():
		print vbd_ref, vbd_info

	session.xenapi.VBD.plug(vbd_ref)

	return True

def _do_main():
	global session, task
	
	if _get_add_vm_disk_task() == False:
		return
	print time.strftime('%Y-%m-%d %X',time.localtime(time.time()))
	print task

	if _init_session() == False:
		return

	_add_vm_disk()

	_set_task_feedback(task['taskid'], 'finish', 0, '', '')

	session.xenapi.session.logout()


if __name__ == "__main__":
	try:
		_do_main()
	except:
		error = traceback.format_exc()
		print time.strftime('%Y-%m-%d %X',time.localtime(time.time()))
		print error

