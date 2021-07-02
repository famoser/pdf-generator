# Resources

Material handy to further development.

## document generation

reference code:
- [ITextSharp](https://www.mikesdotnetting.com/article/80/create-pdfs-in-asp-net-getting-started-with-itextsharp) (nice public API)

## pdf generation
specification:
- [Standard v1.7](https://www.adobe.com/content/dam/acom/en/devnet/pdf/pdfs/PDF32000_2008.pdf) (page 570 long example)
- [Standard v2.0](https://gitlab.com/famoser/pdf-generator-research/-/blob/master/PDF%202020-2.pdf)
    
tools:
- [PDF structure browser](http://podofo.sourceforge.net/tools.html)

tutorials:
- [Guided Hello world](https://blog.idrsolutions.com/2013/01/understanding-the-pdf-file-format-overview/#helloworld) for small examples

not implemented yet:
- chapter 8 (colors)
- chapter 14.3 (metadata)

## ttf

specifications:
- [Microsoft](https://docs.microsoft.com/en-us/typography/opentype/spec/) 
- [Apple](https://developer.apple.com/fonts/TrueType-Reference-Manual/)

reference code:
- https://github.com/nicksagona/popphp-v1-legacy/blob/master/vendor/PopPHPFramework/src/Pop/Font/TrueType.php

tools:
- [FontValidator](https://github.com/HinTak/Font-Validator/) support many different checks
- [FontForge](https://fontforge.org/en-US/) helps to see if characters are correct
- [ttfdump](http://manpages.ubuntu.com/manpages/trusty/man1/ttfdump.1.html) can dump TTFs to ASCII tables

not implemented yet:
- ligatures
- multi-byte characters
