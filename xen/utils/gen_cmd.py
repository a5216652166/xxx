#!/bin/env python
import sys
import os

for i in range(41, 51):
	cmd = "ssh root@10.11.15.%d '%s'"%(i, sys.argv[2])
	print cmd
	if sys.argv[1] == 'run':
		os.system(cmd)
	else:
		continue
print("\n")

