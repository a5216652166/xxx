#!/bin/sh
cd /opt/uwsgi/
#python -u ./pool_vm_list_update.py >> poolvmlistupdate.log 2>&1
python -u ./pool_vm_list_update.py >> /dev/null 2>&1
