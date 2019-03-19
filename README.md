# pdf-generator
[![MIT licensed](https://img.shields.io/badge/license-MIT-blue.svg)](./LICENSE) 
[![FOSSA Status](https://app.fossa.io/api/projects/git%2Bgithub.com%2Ffamoser%2Fpdf-generator.svg?type=shield)](https://app.fossa.io/projects/git%2Bgithub.com%2Ffamoser%2Fpdf-generator?ref=badge_shield)
[![Build Status](https://travis-ci.org/famoser/pdf-generator.svg?branch=master)](https://travis-ci.org/famoser/pdf-generator)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/famoser/pdf-generator/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/famoser/pdf-generator/?branch=master)
[![codecov](https://codecov.io/gh/famoser/pdf-generator/branch/master/graph/badge.svg)](https://codecov.io/gh/famoser/pdf-generator) 


## About
Generates pdf files without any dependencies. An MIT licensed alternative to https://github.com/tecnickcom/TCPDF.

Targets of the compiler:
1. Correctness
    - high unit test coverage (`codecov`)
    - develop by following adobe standard (PDF 1.7)
    - tests with multiple viewers (Adobe Acrobat, Firefox, Edge, Evince)
    - tests on muliple OS (Linux, Windows)
    - cross-reference output with other generators (ITextSharp, TCPDF)
2. Maintainability
    - low cyclomatic complexity (`scrutinizer`)
    - no code smells (`scrutinizer`)
    - classic compiler patterns (frontend, intermediate representation (IR) & backend)
    - clearly defined purpose per namespace (see below for an overview)
3. Small resulting file size
    - elements only included in file if referenced somewhere (ensured by Backend)
    - only new element created if not possible to append to another one (ensured by IR)
4. Speed of compilation
    - only provide single way to accoplish something 
    - no html/css parsing

Overview of the project by namespace:
- `PdfGenerator\Backend\File` contains a direct representation of the file.
- `PdfGenerator\Backend` ensures a valid file is constructed.
- `PdfGenerator\IR` accepts and allows configuration of print commands.
- `PdfGenerator\Frontend` controls where text/images are printed.
- `DocumentGenerator` defines interfaces which are suited for an enduser (not constrained to the PDF format).

Features:
- [x] print text
- [x] print images
- [ ] use TTF fonts
- [ ] print UTF-8 text
- [ ] draw lines
- [ ] draw rectangles
- [ ] public api definition
- [ ] layouts
- [ ] public api implementation
