# Usage:
#     mono ipy.exe this_script.py <fontfile1> <fontfile2> ...

# Copyright (c) Hin-Tak Leung

# Read Created Time and Modified Time from fonts' head table.

import clr
import sys

clr.AddReference("OTFontFile.dll")

from OTFontFile import OTFile

class headtime:
    def __init__(self, filename):
        self.filename = filename
        self.f = OTFile()
        self.f.open(filename)
        for i in range(0, self.f.GetNumFonts()):
            fn = self.f.GetFont(i)
            tname = fn.GetTable("name")
            print tname.GetNameString()
            thead = fn.GetTable("head")
            print "\t%s" % thead.GetCreatedDateTime()
            print "\t%s" % thead.GetModifiedDateTime()

if __name__ == '__main__':
    if not sys.argv[1:]:
        print("Usage: %s fontfiles" % sys.argv[0])

    files = sys.argv[1:]

    for file in files:
        headtimeobj = headtime(file)
