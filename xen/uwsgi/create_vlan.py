import sys
import time
import config
import XenAPI
import traceback
import provision

session = ''


def _do_main():
	global session
	
	master_url = "http://%s/"%('10.11.253.43')
	session = XenAPI.Session(master_url)
	session.xenapi.login_with_password('root', 'Rjkj@efly#123')

	pool_master = ''
	pools = session.xenapi.pool.get_all()
	for pool in pools:
		pool_record = session.xenapi.pool.get_record(pool)
		pool_master = pool_record['master']

	network_cfg = {
					'uuid': '',
					'name_label': 'networkx.vlanx',
					'name_description': 'vlanx desc',
					'VIFs': [],
					'PIFs': [],
					'other_config': {},
					'bridge': '',
					'blobs': {}
				}
	network_cfg['other_config']['automatic'] = "false"
	#print network_cfg
	#network = session.xenapi.network.create(network_cfg) 
	#print network

	print pool_master
	master_record = session.xenapi.host.get_record(pool_master)
	print master_record['name_label']
	#print master_record
	pifs = master_record['PIFs']
	for pif in pifs:
		pif_record = session.xenapi.PIF.get_record(pif)
		if pif_record['physical']:
			print pif, pif_record['network']
			#####
			network = session.xenapi.network.create(network_cfg)
			tag = 3
			session.xenapi.pool.create_VLAN_from_PIF(pif, network, str(tag))

	return

	vlans = session.xenapi.VLAN.get_all()
	for vlan in vlans:
		vlan_record = session.xenapi.VLAN.get_record(vlan)
		pif = vlan_record['tagged_PIF']
		print vlan_record
		pif_record = session.xenapi.PIF.get_record(pif)
		host = pif_record['host']
		host_record = session.xenapi.host.get_record(host)
		print "IP = %s"%(pif_record['IP'])
		print "host_name_label = %s"%(host_record['name_label'])
		print "DNS = %s"%(pif_record['DNS'])
		print "device =%s"%(pif_record['device'])
		print "network = %s"%(pif_record['network'])
		print "MAC = %s"%(pif_record['MAC'])
		print '-------------------------------------------------------'

	session.xenapi.session.logout()

if __name__ == "__main__":
	try:
		_do_main()
	except:
		traceback.print_exc()

