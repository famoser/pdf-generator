# Copyright (c) Hin-Tak Leung

# This is the rasterization parameter module, not intended to be called
# as script. Though it works, as below in __main__ .

import clr
import sys
import System
clr.AddReference("OTFontFileVal.dll")

from OTFontFileVal import ValidatorParameters

from System import Array, Single
from System.Collections.Generic import List

class validation_parameters:
    def __init__(self):
        vp = ValidatorParameters()
        ###############################################################
        # below are just the default values and can be deleted as is. #
        ###############################################################
        # SetAllTables() is the default, but an explicit list also works.
        vp.SetAllTables()
        vp.ClearTables()
        vp.tablesToTest = List[str](['BASE', 'CBDT', 'CBLC', 'CFF ', 'cmap', 'COLR',
                                     'CPAL', 'cvt ', 'DSIG', 'EBDT', 'EBLC', 'EBSC',
                                     'fpgm', 'gasp', 'GDEF', 'glyf', 'GPOS', 'GSUB',
                                     'hdmx', 'head', 'hhea', 'hmtx', 'JSTF', 'kern',
                                     'loca', 'LTSH', 'MATH', 'maxp', 'name', 'OS/2',
                                     'PCLT', 'post', 'prep', 'SVG ', 'VDMX', 'vhea',
                                     'vmtx', 'VORG'])
        vp.doRastBW = False
        vp.doRastGray = False
        vp.doRastClearType = False
        vp.doRastCTCompWidth = False
        vp.doRastCTVert = False
        vp.doRastCTBGR = False
        vp.doRastCTFractWidth = False
        vp.xRes = 96
        vp.yRes = 96
        vp.xform.stretchX = 1.0
        vp.xform.stretchY = 1.0
        vp.xform.rotation = 0.0
        vp.xform.skew     = 0.0
        # Python does not have single precision, but C# does.
        # casting from int to Single is automatic, Double to Single isn't.
        vp.xform.matrix[0,0] = clr.Convert(1.0, Single)
        vp.xform.matrix[0,1] = 0
        vp.xform.matrix[0,2] = 0
        vp.xform.matrix[1,0] = 0
        vp.xform.matrix[1,1] = 1
        vp.xform.matrix[1,2] = 0
        vp.xform.matrix[2,0] = 0
        vp.xform.matrix[2,1] = 0
        vp.xform.matrix[2,2] = 1
        vp.sizes = List[int]([4,5,6,7,8,9,10,
                              11,12,13,14,15,16,17,18,19,20,
                              21,22,23,24,25,26,27,28,29,30,
                              31,32,33,34,35,36,37,38,39,40,
                              41,42,43,44,45,46,47,48,49,50,
                              51,52,53,54,55,56,57,58,59,60,
                              61,62,63,64,65,66,67,68,69,70,
                              71,72,
                              80,88,96,102,110,118,126])
        ###############################################################
        ################## default values end #########################
        ###############################################################
        self.vp = vp

    def GetValue(self):
        return self.vp

if __name__ == '__main__':
    obj = validation_parameters()
    vp = obj.GetValue()
    # Just to confirm expected type and content:
    print "object type %s, xRes = %d" % ( vp.GetType(), vp.xRes)
    # and tables within
    print "Tables to test:", vp.tablesToTest
