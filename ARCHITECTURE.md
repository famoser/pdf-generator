# Architecture of the project

Like any compiler, its divided primarily into Frontend, Intermediate Representation (IR) and Backend.

Targets of the compiler:
- high-quality output (PDF standard 2.0 compliance, no repetition, small filesize)
- maintainable library code (sane compiler patterns, static analysis)
- maintainable user code (no HTML/CSS, single way to accomplish something)

## Frontend

The frontend currently has the following rough architecture:

- *Content* for the actual placed content; such as rectangles, image placements or text.
- *Layout* for defining the layout of the content; such as blocks, flows, grids or tables.
- *Resource* for resources necessary to print the content, such as images or fonts.
- *LayoutEngine* which resolves the layout definition to something printable.

The *LayoutEngine* itself is composed out of the following steps:

- *Measure* allows the layout engine to plan the layout: By calculating minimal space required to print something
  meaningful, and a rough estimate of how much needs to be printed.
- *Allocate* provides content that fits in the given space: Given max dimensions to print in, it calculates the concrete
  content that fits and the space this requires.
- *Print* prints the calculated allocation. As this is a separate step, users can decide whether to abort printing
  depending on the result of the allocation.

The frontend may become the backend of more abstract document generation library, and its frontend may then unify
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
