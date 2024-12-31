<?php

namespace Famoser\PdfGenerator\Backend\Structure\Document\Page\Content\Paragraph;

enum Role: string
{
    case Title = 'Title';
    case H1 = 'H1';
    case H2 = 'H2';
    case H3 = 'H3';
    case H4 = 'H4';
    case H5 = 'H5';
    case P = 'P';
    case Aside = 'Aside';
}
