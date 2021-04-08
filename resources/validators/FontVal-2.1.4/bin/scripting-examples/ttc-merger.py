# Usage:
#     mono ipy.exe ttc-merger.py outfile infile1 infile2 ... infileN

# Copyright (c) Hin-Tak Leung

import clr
import sys

clr.AddReference("OTFontFile.dll")

from OTFontFile import OTFile

from System import Array, Console

if __name__ == '__main__':
    if not sys.argv[1:]:
        print("Usage: %s outfile infile1 infile2 ... infileN" % sys.argv[0])

    newfont = Array.CreateInstance(OTFont, len(sys.argv)-2)
    for i in range(2, len(sys.argv)):
        f = OTFile()
        f.open(sys.argv[i])
        newfont[i-2] = f.GetFont(0)
    OTFile.WriteFile(sys.argv[1], newfont)
