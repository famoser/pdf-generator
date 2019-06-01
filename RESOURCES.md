# Resources
material to work through

## pdf generation
- [Adobe standard](https://www.adobe.com/content/dam/acom/en/devnet/pdf/pdfs/PDF32000_2008.pdf) for the full spec
    - read chapter 8 to resolve coloring issues
    - implement 14.3 for nice metadata
    - see page 570 for a longer example
- [Guided Hello world](https://blog.idrsolutions.com/2013/01/understanding-the-pdf-file-format-overview/#helloworld) for small examples
- [PDF structure browser](http://podofo.sourceforge.net/tools.html)

## public api
- [ITextSharp](https://www.mikesdotnetting.com/article/80/create-pdfs-in-asp-net-getting-started-with-itextsharp) because has nice public api

## ttf

- ttf are TrueType fonts, page 259
- they can be included directly in the pdf, but there has to be an encoding defined
- https://github.com/nicksagona/popphp-v1-legacy/blob/master/vendor/PopPHPFramework/src/Pop/Font/TrueType.php
- ttfdump to check output
