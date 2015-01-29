#!/bin/sh
cd /opt/uwsgi/
python -u ./create_vm_task.py >> createvmtask.log 2>&1
