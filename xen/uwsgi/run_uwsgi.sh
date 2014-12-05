#!/bin/sh

#
# http://61.142.208.98:8088/uwsgi/?module=ecloud_mytest&arg1=1&arg2=2
#

./uwsgi -s 127.0.0.1:3031 -M -p 4 --pythonpath /opt/uwsgi --chdir /opt/uwsgi --wsgi-file main.py --daemonize /var/log/uwsgi.log 
