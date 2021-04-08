# Usage:
#     mono ipy.exe this_script.py args1 args2 args3 ...

# Copyright (c) Hin-Tak Leung

# This script just writes the args out, for basic testing.

import sys

print __name__

if __name__ == '__main__':
    i = 0
    for arg in sys.argv:
        print "arg%d: '%s'" % (i, arg)
        i += 1
