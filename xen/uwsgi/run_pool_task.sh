#!/bin/sh
cd /opt/uwsgi/
python -u ./pool_task_main.py >> pooltask.log 2>&1
