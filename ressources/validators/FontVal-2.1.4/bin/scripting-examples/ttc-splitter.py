# Usage:
#     mono ipy.exe ttc-splitter.py infile outfile
#     [writes outfile.0,outfile.1, etc for member fonts]

# Copyright (c) Hin-Tak Leung

import clr
import sys

clr.AddReference("OTFontFile.dll")

from OTFontFile import OTFile

from System import Array

if __name__ == '__main__':
    if not sys.argv[1:]:
        print("Usage: %s infile outfile" % sys.argv[0])

    infile = sys.argv[1]
    outfile = sys.argv[2]

    f = OTFile()
    f.open(infile)
    for i in range(0, f.GetNumFonts()):
        fn = f.GetFont(i)
        newout = "%s.%d" % (outfile, i)
        memfont = Array[OTFont]([fn])
        OTFile.WriteFile(newout, memfont)
