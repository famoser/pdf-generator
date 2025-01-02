# Architecture of the project

Like any compiler, its divided primarily into Frontend, Intermediate Representation (IR) and Backend.

Targets of the compiler:
- high-quality output (PDF standard 1.7 compliance, no repetition, small filesize)
- maintainable library code (sane compiler patterns, static analysis)
- maintainable user code (no HTML/CSS, single way to accomplish something)

## Frontend

The frontend currently has the following rough architecture:

- *Layout* for defining the layout of the content; such as blocks, flows, grids, tables or text.
- *LayoutEngine* which resolves the layout definition to something printable (the Content).
- *Content* for the ready-to-print content; such as rectangles, image placements or dimensioned text.
- *Resource* for resources necessary to print the content, such as images or fonts.

The *LayoutEngine* itself is composed out of the following steps:

- *Measure* allows the layout engine to plan the layout: By calculating minimal space required to print something
  meaningful, and a rough estimate of how much needs to be printed.
- *Allocate* provides content that fits in the given space: Given max dimensions to print in, it calculates the concrete
  content that fits and the space this requires.

The user interacts with the frontend using:

- *Document* to add high-level elements such as tables from the Layout namespace.
- *Printer* to print low-level content such as rectangles from the Content namespace

## IR

The Intermediate Representation provides a paged document that fully represents what the user wants to print. It is 
called by the frontend whenever some element is fully defined by the user. Once the document is finalized, the elements 
are optimized (e.g. images are resized), and then transformed to the representation the backend expects.

It is structured mainly into:

- *Document* which stores content (e.g. text) and resources (e.g. images) as read-only entities.
- *Analysis* which passes through the entire document to gather overall statistics (e.g. image size / character sets in
  use).

## Backend

The Backend provides an API close to the PDF spec. It is itself divided into multiple parts:

- *Structure* contains a minimal structure of supported structures by the backend (hence the "frontend" for the IR). It
  renders content types such as text / images into a stream consumable for PDFs, and creates the catalog structure of
  the PDF.
- *Catalog* contains the logical structure of a PDF. It is capable of converting this logical structure into a structure
  using only streams and dictionaries (the first higher-level structures of a PDF).
- *File* contains the structure of a PDF as it can be written to a file. It can setup the file header/trailer/cross
  reference table given the body of the file (streams/dictionaries). Lastly, it converts the body to tokens and then
  writes the content of the resulting file.
