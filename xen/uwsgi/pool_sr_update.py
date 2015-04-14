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

	#master_url = "http://%s/"%(pool_data['master'])
	rpc_url = pool_data['xen-api-rpc']
	session = XenAPI.Session(rpc_url)
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

def _update_pool_sr_info(pool_code, sr_info):
	conn = MySQLdb.connect(host=config.ecloud_db_ip, user=config.ecloud_db_user, \
							passwd=config.ecloud_db_pass, db='ecloud_admin', port=3306)
	cur = conn.cursor(cursorclass = MySQLdb.cursors.DictCursor)
	sql = "update `Storage` set `Total` = '%d', `Used` = '%d', `TimeStamp` = now() "\
		" where `UUID` = '%s';"	%(sr_info['total'], sr_info['used'], sr_info['uuid'])
	#print sql
	cur.execute(sql)

def _get_pool_srs_info(pool_code, pool_data):

	session = _init_pool_session(pool_code, pool_data)
	srs = session.xenapi.SR.get_all()
	for sr in srs:
		#print sr
		record = session.xenapi.SR.get_record(sr)
		name_label = record['name_label']
		if not name_label.startswith('EQL-'):
			continue
		sr_name = record['name_label']
		sr_uuid = record['uuid']
		sr_total = float(record['physical_size'])/1024/1024/1024
		sr_used = float(record['virtual_allocation'])/1024/1024/1024
		sr_used_per = int(sr_used/sr_total*100)
		#print sr_uuid, sr_name, sr_used_per, sr_total, sr_used
		sr_info = { 'uuid':sr_uuid, \
					'name':sr_name, \
					'total':int(sr_total), \
					'used':int(sr_used) }
		print sr_info
		_update_pool_sr_info(pool_code, sr_info)
	session.xenapi.session.logout()

def _do_main():
	global pools
	
	if _get_all_pool_data() == False:
		return
	#print pools
	for pool_code, pool_data in pools.items():
		#print pool_code, pool_data['data']
		_get_pool_srs_info(pool_code, pool_data['data'])


if __name__ == "__main__":
	try:
		_do_main()
	except:
		error = traceback.format_exc()
		print error

