import sys
import time
import config
import XenAPI
import traceback
import provision

session = ''


def _do_main():
	global session
	
	session = XenAPI.Session('http://121.201.60.66:8000')
	session.xenapi.login_with_password('root', 'Rjkj@free7248#8')

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

	#print pool_master
	master_record = session.xenapi.host.get_record(pool_master)
	print("master=%s\n"%(master_record['name_label']))
	#print master_record
	pifs = master_record['PIFs']
	for pif in pifs:
		pif_record = session.xenapi.PIF.get_record(pif)
		if not pif_record['physical']:
			if not pif_record['VLAN'] == '-1':
				continue
			if not pif_record['device'] == 'bond0':
				continue
			#print pif, pif_record['network'], pif_record['device'], pif_record['bond_slave_of'], pif_record['VLAN']
			#print pif_record
			#print "\n\n"
			#continue

			network_record = session.xenapi.network.get_record(pif_record['network'])
			#print network_record
			network_name_label = network_record['name_label']
			network_name_description = network_record['name_description']
			print("name=[%s] desc=[%s]\n" %(network_name_label, network_name_description))
			#continue

			#####
			for tag in range(7, 130):
				vlan_name_label = "bond0.vlan.%d"%(tag)
				vlan_name_description = "produce network for vlan %d"%(tag)
				print("create vlan %s.vlan.%d "%(network_name_label, tag))
				network_cfg['name_label'] = vlan_name_label
				network_cfg['name_description'] = vlan_name_description
				network = session.xenapi.network.create(network_cfg)
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

