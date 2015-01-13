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

pools = {}

def _init_pool_session(pool_code, pool_data):
	# {u'vm_code': u'A-08-509-26-20141211-016', 
	# 'pool_code': 'P-26-001', u'ram': u'2048', u'cpu': u'4', 
	# u'template_code': u'Template_CentOS_6.5_x86_64'}
	#
	#print pool_data
	# {u'uuid': u'7106005d-e8e3-4da5-8adc-4e8035da77ad', 
	# u'master': u'10.11.253.43', u'user': u'root', u'pass': u'Rjkj@efly#123'}

	master_url = "http://%s/"%(pool_data['master'])
	session = XenAPI.Session(master_url)
	session.xenapi.login_with_password(pool_data['user'], pool_data['pass'])
	return session

def _get_all_pool_data():
	global pools

	conn = MySQLdb.connect(host=config.ecloud_db_ip, user=config.ecloud_db_user, \
							passwd=config.ecloud_db_pass, db='ecloud_admin', port=3306)
	cur = conn.cursor(cursorclass = MySQLdb.cursors.DictCursor)
	#cur.execute("select * from `Pool` where `State` = 'enabled' and `PoolCode` like 'P-%-%';")
	cur.execute("select * from `Pool` where `State` = 'enabled';")
	rows = cur.fetchall()
	if rows == None:
		return False

	for row in rows:
		pool_code = row['PoolCode']
		pool_data = json.loads(row['Data'])
		pools[pool_code] = { 'data':pool_data }

	return True

def _update_pool_vm_info(pool_code, vm_code, vm_info):
	conn = MySQLdb.connect(host=config.ecloud_db_ip, user=config.ecloud_db_user, \
							passwd=config.ecloud_db_pass, db='ecloud_admin', port=3306)
	cur = conn.cursor(cursorclass = MySQLdb.cursors.DictCursor)
	sql = "update `VM` set `PowerState` = '%s', "\
		"`Cpu` = '%s', `Ram` = '%s', `Disk` = '%s', `HostCode` = '%s', "\
		"`TimeStamp` = now() where `VMCode` = '%s';"\
		%(vm_info['power_state'], vm_info['cpu'], vm_info['ram'], vm_info['disk'], vm_info['host'], vm_code)
	#print sql
	cur.execute(sql)

def _get_pool_vm_list(pool_code, pool_data):

	session = _init_pool_session(pool_code, pool_data)
	vms = session.xenapi.VM.get_all()
	for vm in vms:
		record = session.xenapi.VM.get_record(vm)
		if record["is_a_template"] or record["is_control_domain"]:
			continue
		#print record['uuid'], record['name_label'], record['power_state']
		#print record['name_label'], record['VCPUs_max'], record['memory_static_max']

		#get host code
		host = record['resident_on']
		host_code = session.xenapi.host.get_name_label(host)
		#print record['name_label'], host, host_code

		#get ram
		ram = int(int(record['memory_static_max']) / 1024 / 1024)

		#get disk virtual size
		vdisk_size = 0
		for vbd in record['VBDs']:
			vbd_record = session.xenapi.VBD.get_record(vbd)
			if vbd_record['type'] != 'Disk':
				continue
			vdi = vbd_record['VDI']
			if vdi == 'OpaqueRef:NULL':
				continue
			vdi_record = session.xenapi.VDI.get_record(vdi)
			vsize = int(vdi_record['virtual_size']) / 1024 / 1024 / 1024
			#print vbd_record['device'], vbd_record['type'], vbd_record['bootable'], vbd_record['userdevice'], vsize
			#xvda Disk True 0 20
			#xvdb Disk False 1 10
			if vbd_record['device']	== 'xvda':
				vdisk_size = vsize

		vm_code = record['name_label']
		vm_info = { 'power_state':record['power_state'], \
					'cpu':record['VCPUs_max'], \
					'ram':str(ram), \
					'disk':str(vdisk_size), \
					'host':host_code }
		_update_pool_vm_info(pool_code, vm_code, vm_info)
	session.xenapi.session.logout()

def _do_main():
	global pools
	
	if _get_all_pool_data() == False:
		return
	#print pools
	for pool_code, pool_data in pools.items():
		#print pool_code, pool_data['data']
		_get_pool_vm_list(pool_code, pool_data['data'])

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

