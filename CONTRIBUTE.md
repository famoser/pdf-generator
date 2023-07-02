# Contribute

Contributions are very welcome. Before investing serious effort, please create an issue to discuss target functionality
and architecture.

TTF reader/writer:

- [x] read TTF
- [x] write TTF
- [x] create TTF subsets
- [x] provide font dimensions to measure text

PDF backend:

- [x] print images
- [x] print & style drawings (lines & rectangles)
- [x] print & style text
- [x] use TTF fonts
- [x] use UTF-8 text

paragraphs:

- [x] support different text styles in same paragraphs
- [x] calculate dimensions of text
- [x] automatic line-breaking
- [ ] alignment (center, right-align, justify)

layouts:

- [x] design layout system
- [x] implement block
- [x] implement flow
- [ ] implement grid
- [ ] implement table

layout blocks:

- [x] margin
- [x] padding
- [x] border (color, thickness, stroke style)
- [x] background (color)

support more content types:

- [ ] png, svg, esp, ...
- [ ] video, audio, ... (?)

extend layout support:

- [ ] improve line breaking (knuth & plass line-breaking instead of greedy)
- [ ] alignment for blocks
- [ ] column/row spans for grids, tables
- [ ] auto, contain, cover for content types

extend drawing support:

- [ ] circles
- [ ] polynomials

extend PDF support:

- [ ] forms
- [ ] compress string streams
- [ ] optimize rectangle position (do not modify transform matrix)
