#!/bin/sh
cd /opt/uwsgi/
python -u ./pool_sr_update.py >> /dev/null 2>&1
