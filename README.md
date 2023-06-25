# pdf-generator

[![MIT licensed](https://img.shields.io/badge/license-MIT-blue.svg)](./LICENSE)
[![PHP Composer](https://github.com/famoser/pdf-generator/actions/workflows/php.yml/badge.svg)](https://github.com/famoser/pdf-generator/actions/workflows/php.yml)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/famoser/pdf-generator/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/famoser/pdf-generator/?branch=main)
[![Scrutinizer Coverage](https://scrutinizer-ci.com/g/famoser/pdf-generator/badges/coverage.png?b=main)](https://scrutinizer-ci.com/g/famoser/pdf-generator/?branch=main)

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

The basic concept is that a user chooses a layout (e.g. a grid), and then adds content to that layout (e.g. a
paragraph). This can be done recursively. Importantly, the layouts also "flow": If too much content is available for the
space left (e.g. on the same page), only parts are printed.

For the layout system, the following design decisions were taken:

- `width` (`height`) includes the `padding`, but not the `margin`
- `margin` does not collapse with adjacent margins
- drawings are ignored during size calculations (e.g. `borderWidth` has no influence on the calculated `width` of an
  element
- `width` (`height`) overrides calculated `maxWidth` (`maxHeight`)

The frontend currently has the following rough architecture:

- *Measure* allows the layout engine to plan the layout: By calculating minimal space required to print something
  meaningful, and a rough estimate of how much needs to be printed.
- *Allocate* provides content that fits in the given space: Given max dimensions to print in, it calculates the concrete
  content that fits and the space this requires.
- *Print* puts content on a page: Given fixed dimensions to print it, it places content on the document that fits, but
  guarantees progress (hence might print something that does not fit).

The frontend then may become the backend of more abstract document generation library, and this frontend may then unify
a way e.g. generate both HTML and PDF documents using the same code. Experiments towards this are done in
the `document-generator` folder.

### IR

The Intermediate Representation provides an API that is capable of transforming itself to something the backend can work
with. The API is largely PDF-independent (i.e. might apply to other paginated document formats).

It is structured mainly into:

- *Document* which stores content (e.g. text) and resources (e.g. images) as read-only entities.
- *Analysis* which passes through the entire document to gather overall statistics (e.g. image size / character sets in
  use).

### Backend

The Backend itself is divided into multiple parts

- *Content* contains a minimal structure of supported structures by the backend (hence the "frontend" for the IR). It
  renders content types such as text / images into a stream consumable for PDFs, and creates the catalog structure of
  the PDF.
- *Catalog* contains the logical structure of a PDF. It is capable of converting this logical structure into a structure
  using only streams and dictionaries (the first higher-level structures of a PDF).
- *File* contains the structure of a PDF as it can be written to a file. It can setup the file header/trailer/cross
  reference table given the body of the file (streams/dictionaries). Lastly, it converts the body to tokens and then
  writes the content of the resulting file.

## Timeline

The project will be developed in multiple phases.

### Render PDF Milestone

First, the backend will be created following closely the standard of adobe.

- [x] print & style text
- [x] print images
- [x] print & style drawings (lines & rectangles)
- [x] use TTF fonts
- [x] print UTF-8 text
    - [x] check if all characters correctly included in font (like ä)
    - [x] make font dimensions available in the IR (to measure text)

### Minimal IR Base

To be able to print to the pdf sensible some initial works needs to be done to see whats doable and what is not.

- [x] place text on pages with automatic breaks

### Text API Milestone

Then, the public API will be defined.

text printing:

- [x] calculate dimensions of text
- [x] paragraphs
- [x] different styles in same paragraphs
- [x] measurement
- [x] automatic line-breaking
- [ ] alignment (center, right-align, justify)

layouts:

- [x] flows (rows, columns)
- [x] grid
- [x] table

styling:

- [x] margin
- [x] padding
- [x] border (color, thickness, stroke style)
- [x] background (color)

### Multimedia API Milestone

extend functionality to more use-cases.

content types:

- [ ] png, svg, esp, ...
- [ ] video, audio, ... (?)

sizing / positioning:

- [ ] column span for columns
- [ ] column/row span for grids
- [ ] auto, contain, cover for content types
- [ ] float for blocks

drawing:

- [ ] circles
- [ ] polynomials

PDF features:

- [ ] forms

### Fun Milestone

What does not need to be done, but could.

text:

- [ ] better line breaking (knuth & plass line-breaking instead of greedy)

file:

- [ ] compress string streams
- [ ] optimize rectangle position (do not modify transform matrix)
