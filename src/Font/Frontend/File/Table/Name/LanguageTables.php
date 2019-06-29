<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\Frontend\File\Table\Name;

class LanguageTables
{
    /**
     * table from https://developer.apple.com/fonts/TrueType-Reference-Manual/RM06/Chap6name.html.
     *
     * @param int $languageID
     *
     * @return array
     */
    public static function getMacintoshLanguageName(int $languageID)
    {
        return [
            0 => 'English', 59 => 'Pashto',
            1 => 'French', 60 => 'Kurdish',
            2 => 'German', 61 => 'Kashmiri',
            3 => 'Italian', 62 => 'Sindhi',
            4 => 'Dutch', 63 => 'Tibetan',
            5 => 'Swedish', 64 => 'Nepali',
            6 => 'Spanish', 65 => 'Sanskrit',
            7 => 'Danish', 66 => 'Marathi',
            8 => 'Portuguese', 67 => 'Bengali',
            9 => 'Norwegian', 68 => 'Assamese',
            10 => 'Hebrew', 69 => 'Gujarati',
            11 => 'Japanese', 70 => 'Punjabi',
            12 => 'Arabic', 71 => 'Oriya',
            13 => 'Finnish', 72 => 'Malayalam',
            14 => 'Greek', 73 => 'Kannada',
            15 => 'Icelandic', 74 => 'Tamil',
            16 => 'Maltese', 75 => 'Telugu',
            17 => 'Turkish', 76 => 'Sinhalese',
            18 => 'Croatian', 77 => 'Burmese',
            19 => 'Chinese (traditional)', 78 => 'Khmer',
            20 => 'Urdu', 79 => 'Lao',
            21 => 'Hindi', 80 => 'Vietnamese',
            22 => 'Thai', 81 => 'Indonesian',
            23 => 'Korean', 82 => 'Tagalog',
            24 => 'Lithuanian', 83 => 'Malay (Roman script)',
            25 => 'Polish', 84 => 'Malay (Arabic script)',
            26 => 'Hungarian', 85 => 'Amharic',
            27 => 'Estonian', 86 => 'Tigrinya',
            28 => 'Latvian', 87 => 'Galla',
            29 => 'Sami', 88 => 'Somali',
            30 => 'Faroese', 89 => 'Swahili',
            31 => 'Farsi/Persian', 90 => 'Kinyarwanda/Ruanda',
            32 => 'Russian', 91 => 'Rundi',
            33 => 'Chinese (simplified)', 92 => 'Nyanja/Chewa',
            34 => 'Flemish', 93 => 'Malagasy',
            35 => 'Irish Gaelic', 94 => 'Esperanto',
            36 => 'Albanian', 128 => 'Welsh',
            37 => 'Romanian', 129 => 'Basque',
            38 => 'Czech', 130 => 'Catalan',
            39 => 'Slovak', 131 => 'Latin',
            40 => 'Slovenian', 132 => 'Quechua',
            41 => 'Yiddish', 133 => 'Guarani',
            42 => 'Serbian', 134 => 'Aymara',
            43 => 'Macedonian', 135 => 'Tatar',
            44 => 'Bulgarian', 136 => 'Uighur',
            45 => 'Ukrainian', 137 => 'Dzongkha',
            46 => 'Byelorussian', 138 => 'Javanese (Roman script)',
            47 => 'Uzbek', 139 => 'Sundanese (Roman script)',
            48 => 'Kazakh', 140 => 'Galician',
            49 => 'Azerbaijani (Cyrillic script)', 141 => 'Afrikaans',
            50 => 'Azerbaijani (Arabic script)', 142 => 'Breton',
            51 => 'Armenian', 143 => 'Inuktitut',
            52 => 'Georgian', 144 => 'Scottish Gaelic',
            53 => 'Moldavian', 145 => 'Manx Gaelic',
            54 => 'Kirghiz', 146 => 'Irish Gaelic (with dot above)',
            55 => 'Tajiki', 147 => 'Tongan',
            56 => 'Turkmen', 148 => 'Greek (polytonic)',
            57 => 'Mongolian (Mongolian script)', 149 => 'Greenlandic',
            58 => 'Mongolian (Cyrillic script)', 150 => 'Azerbaijani (Roman script)',
        ];
    }
}
