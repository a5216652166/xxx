#!/bin/sh
cd /opt/uwsgi/
python -u ./pool_vm_list_update.py >> poolvmlistupdate.log 2>&1
