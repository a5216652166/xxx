#!/bin/env python
import traceback
import commands
import urllib

def do_main():
    (status, output) = commands.getstatusoutput('/usr/bin/xenstore-list /local/domain')
    if status != 0:
        return
    domids = output.split()
    #print domids
    for domid in domids:
        cmd = "/usr/bin/xenstore-read /local/domain/%s/name"%(domid) 
        (status, output) = commands.getstatusoutput(cmd)
        if status != 0:
            continue
        vm_name = output.strip("\n")

        cmd = "/usr/bin/xenstore-read /local/domain/%s/console/vnc-port"%(domid)
        (status, output) = commands.getstatusoutput(cmd)
        if status != 0:
            continue
        vnc_port = output.strip("\n")

        cmd = "/usr/bin/xenstore-read /local/domain/%s/console/tty"%(domid)
        (status, output) = commands.getstatusoutput(cmd)
        if status != 0:
            continue
        tty = output.strip("\n")

        print vm_name, vnc_port, tty
        params = urllib.urlencode({'VMCode':vm_name, 'VNCPort':vnc_port, 'TTY':tty})
        try:
            f = urllib.urlopen("http://xen-http-proxy:8000/ecloud_admin/UpdateVMConsole.php?%s"%(params))
            f.read()   
        except:
            traceback.print_exc()

if __name__ == "__main__":
    try:
        do_main()
    except:
        traceback.print_exc()

