#!/bin/sh
cd /opt/uwsgi/
python -u ./add_vm_disk_task.py >> addvmdisktask.log 2>&1
