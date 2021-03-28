# Usage:
#     mono ipy.exe ttx-l-example.py <fontfile1> <fontfile2> ...

# This script does the same thing as "ttx -l", except
# it will recurse through member fonts in a ttc and also take
# multiple font file arguments.

# Copyright (c) Hin-Tak Leung

import clr
import sys

# For "clr.Convert(, String)" below, to call the custom implicit cast operator.
# de.tag.ToString()/String(de.tag) does de.tag.GetType() instead.
from System import String

clr.AddReference("OTFontFile.dll")

from OTFontFile import OTFile

class ttxl:
    def __init__(self, filename):
        self.filename = filename
        self.f = OTFile()
        self.f.open(filename)
        for i in range(0, self.f.GetNumFonts()):
            fn = self.f.GetFont(i)
            tname = fn.GetTable("name")
            print tname.GetNameString()
            print "Name\tCheckSum\tLength\tOffset"
            print "========================================="
            for j in range(0, fn.GetNumTables()):
                de = fn.GetDirectoryEntry(j)
                print "%s\t0x%s\t%d\t%d" % (clr.Convert(de.tag, String),
                                            de.checkSum.ToString("X8"), de.length, de.offset)
            print
        if (self.f.IsCollection()):
            ttch = self.f.GetTTCHeader()
            if ((clr.Convert(ttch.DsigTag, String) == "DSIG") and (ttch.DsigOffset > 0)):
                print "DSIG (offset,length):\t%d,%d" % (ttch.DsigOffset, ttch.DsigLength)

if __name__ == '__main__':
    if not sys.argv[1:]:
        print("Usage: %s fontfiles" % sys.argv[0])

    files = sys.argv[1:]

    for file in files:
        ttxlobj = ttxl(file)
