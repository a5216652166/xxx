#!/bin/sh
cd /opt/ECloud-Admin/
python -u ./vm_network_config_task.py >> vmnetworkconfig.log 2>&1
