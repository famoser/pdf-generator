# Architecture of the project

Like any compiler, its divided primarily into Frontend, Intermediate Representation (IR) and Backend.

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

## Frontend

The frontend currently has the following rough architecture:

- *Content* for the actual placed content; such as rectangles, image placements or text
- *Layout* for defining the layout of the content; such as blocks, flows, grids or tables
- *Resource* for resouces necessary to print the content, such as images or fonts
- *LayoutEngine* which resolves the layout definition to something printable

The *LayoutEngine* itself is composed out of the following steps:

- *Measure* allows the layout engine to plan the layout: By calculating minimal space required to print something
  meaningful, and a rough estimate of how much needs to be printed.
- *Allocate* provides content that fits in the given space: Given max dimensions to print in, it calculates the concrete
  content that fits and the space this requires.
- *Print* puts content on a page: Given fixed dimensions to print it, it places content on the document that fits, but
  guarantees progress (hence might print something that does not fit).

The frontend may become the backend of more abstract document generation library, and this frontend may then unify
a way e.g. generate both HTML and PDF documents using the same code. Experiments towards this are done in
the `document-generator` folder.

## IR

The Intermediate Representation provides an API that is capable of transforming itself to something the backend can work
with. The API is largely PDF-independent (i.e. might apply to other paginated document formats).

It is structured mainly into:

- *Document* which stores content (e.g. text) and resources (e.g. images) as read-only entities.
- *Analysis* which passes through the entire document to gather overall statistics (e.g. image size / character sets in
  use).

## Backend

The Backend itself is divided into multiple parts

- *Content* contains a minimal structure of supported structures by the backend (hence the "frontend" for the IR). It
  renders content types such as text / images into a stream consumable for PDFs, and creates the catalog structure of
  the PDF.
- *Catalog* contains the logical structure of a PDF. It is capable of converting this logical structure into a structure
  using only streams and dictionaries (the first higher-level structures of a PDF).
- *File* contains the structure of a PDF as it can be written to a file. It can setup the file header/trailer/cross
  reference table given the body of the file (streams/dictionaries). Lastly, it converts the body to tokens and then
  writes the content of the resulting file.
