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
    - only provide single way to accomplish something 
    - no html/css parsing

## Architecture of the project

Like any compiler, its divided primarily into Frontend, Intermediate Representation (IR) and Backend.

### Frontend
The frontend contains the API the user works with. It should be easy to understand, 
but not hinder the user to access any feature of the backend it may need.

To design an API that is truly easy to understand, 
focusing on the content the user wants to print, but not on PDF specific details,
this is done closely together with the document-generator project.

The `document-generator project` will provide an way to generate documents
using an easy to test API which supports multiple generators.
One of these generators will be this pdf-generator, another one will generate HTML/CSS.

The frontend currently builds on a Layout-Print-Transaction model.

#### Layout
The library provides layouts to position elements on the final document.
For more complex documents, multiple layouts can also be combined together.

#### Print
Using the layout, a cursor can be positioned.

Then a print action can be executed, which prints text/images or other inside the boundaries as set by the layout.

#### Transaction
Before any printed content appears on the document, a transaction has to be created.

The transaction knows its position and appearance on the final document, 
hence is useful to investigate everything will appear as expected and to position future layouts.

If the transaction is of the expected form, it can be committed and will be written to the final document.

### IR
The Intermediate Representation provides an API that is convenient to use for the frontend.
This structure could apply to any paginated document; hence does not expose PDF-specific details.

#### Structure
Contains the structure of the IR.
It can convert the user input to data supported by the backend (text encoding, image resizing).

### Backend
The Backend itself is divided into multiple parts.

#### Content
Contains a minimal structure of supported structures by the backend.  
It renders content types such as text / images into a stream consumable for PDFs.
It creates the catalog structure of the PDF.

#### Catalog
Contains the logical structure of a PDF.
It converts this logical structure into a structure using only streams and dictionaries (the first higher-level structures of a PDF).

#### File
Contains the structure of a PDF as it can be written to a file.
It can setup the the file header/trailer/cross reference table given the body of the file (streams/dictionaries).
It converts the body to tokens and then writes the content of the resulting file.

## Timeline

The project will be developed in multiple phases. 

### Render PDF Milestone
First, the backend will be created following closely the standard of adobe.

- [x] print & style text
- [x] print images
- [x] print & style drawings (lines & rectangles)
- [x] use TTF fonts
- [x] print UTF-8 text
    - [ ] check if all characters correctly included in font (like ä)
    - [ ] make font dimensions available in the IR (to measure text)

### Minimal IR Base
To be able to print to the pdf sensible some initial works needs to be done to see whats doable and what is not.

- [ ] calculate dimensions of tex
- [ ] place text on pages with automatic breaks

### Public API Milestone
Then, the public API will be defined.

- [ ] public api definition
- [ ] layout definition
- [ ] implement sample report

### Final Milestone
Lastly, the final API spec will be implemented using the IR.

- [ ] define sensible way to print to layouted part
- [ ] implement layouts

### Fun Milestone
What does not need to be done, but could.

- [ ] compress string streams
- [ ] optimize rectangle position (do not modify transform matrix)
