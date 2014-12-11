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

	pifs = session.xenapi.PIF.get_all_records()
	lowest = None
	for pifRef in pifs.keys():
		if (lowest is None) or (pifs[pifRef]['device'] < pifs[lowest]['device']):
			lowest = pifRef
	print "Choosing PIF with device: ", pifs[lowest]['device']

	network = session.xenapi.PIF.get_network(lowest)
	print "Chosen PIF is connected to network: ", session.xenapi.network.get_name_label(network)

	temp_vm = session.xenapi.VM.get_by_uuid('7492e6b6-ccb6-e333-5c81-032eff6bad3b')
	temp_vm_name = session.xenapi.VM.get_name_label(temp_vm)
	print temp_vm, temp_vm_name

	st = time.localtime(time.time())
	new_vm_name = "VM%d%02d%02d%02d%02d"%(st.tm_year, st.tm_mon, st.tm_mday, st.tm_hour, st.tm_min)
	new_vm = session.xenapi.VM.clone(temp_vm, new_vm_name)
	print "New VM has name: " + new_vm_name

	#print "Adding noniteractive to the kernel commandline"
	session.xenapi.VM.set_PV_args(new_vm, "noninteractive")

	print "Asking server to provision storage from the template specification"
	session.xenapi.VM.provision(new_vm)

	print "Starting VM"
	session.xenapi.VM.start(new_vm, False, True)

	print "Waiting for the installation to complete"

	while read_os_name(new_vm) == None: time.sleep(1)
	print "Reported OS name: ", read_os_name(new_vm)

	#while read_ip_address(new_vm) == None: time.sleep(1)
	#print "Reported IP: ", read_ip_address(new_vm)

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
		traceback.print_exc()

