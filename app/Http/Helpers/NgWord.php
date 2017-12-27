<?php

namespace App\Http\Helpers;

class NgWord
{
    //List of all ng_words. Last update: 03/17/2015
    private static $ngwords = array(
        1 => 'Fuck',
        2 => 'Mother fuck',
        3 => 'shit',
        4 => 'bull shit',
        5 => 'poo',
        6 => 'pee',
        7 => 'piss',
        8 => 'boobs',
        9 => 'cunt',
        10 => 'pussy',
        11 => 'douchebag',
        12 => 'dickwad',
        13 => 'milf',
        14 => 'whore',
        15 => 'slut',
        16 => 'hooker',
        17 => 'pubes',
        18 => 'clit',
        19 => 'porn',
        20 => 'three sum',
        21 => 'blowjob',
        22 => 'dickface',
        23 => 'clit',
        24 => 'pussy',
        25 => 'cockface',
        26 => 'ass',
        27 => 'asshole',
        28 => 'orgy',
        29 => 'asshole',
        30 => 'duche',
        31 => 'duchebag',
        32 => 'cum',
        33 => 'tits',
        34 => 'cock',
        35 => 'balls',
        36 => 'duchenozzle',
        37 => 'bitch',
        38 => 'anal',
        39 => 'cock',
        40 => 'faggot',
        41 => 'nigga',
        42 => 'nigger',
        43 => 'apic',
        44 => 'wetback',
        45 => 'kike',
        46 => 'viagra',
        47 => 'vigina',
        48 => 'sperms',
        49 => 'dildo',
        50 => 'rejaculate',
        51 => 'nipples',
        52 => 'BDSM',
        53 => 'glasscutters',
        54 => 'idiots',
        55 => 'boobies',
        56 => 'masturbation',
        57 => 'Cougar',
        58 => 'Pornstar',
        59 => 'doggy style',
        60 => 'Fellatio',
        61 => 'Bukkake',
        62 => 'Fucking',
        63 => 'Slow Fuck',
        64 => 'Missionary',
        65 => 'Doggy',
        66 => 'Cowgirl',
        67 => 'Masturbating',
        68 => 'Piledriver',
        69 => 'Cunnilingus',
        70 => 'Pumping',
        71 => 'Lap Dance',
        72 => 'Anal',
        73 => 'Kissing',
        74 => 'Foreplay',
        75 => 'Tits',
        76 => 'Acrobatic',
        77 => 'Fingering',
        78 => 'Fisting',
        79 => 'Indian Sex',
        80 => 'Teabagging',
        81 => 'Bitch',
        82 => 'Dirty',
        83 => 'Footjob',
        84 => 'Armpit',
        85 => 'Boobs',
        86 => 'Busty',
        87 => 'Bigtits',
        88 => 'Juggs',
        89 => 'Mammary',
        90 => 'Small Tits',
        91 => 'Natural Tits',
        92 => 'Cleavage',
        93 => 'Titjob',
        94 => 'Tittyfuck',
        95 => 'Nipples',
        96 => 'Milf',
        97 => 'Pussy',
        98 => 'Muff',
        99 => 'Cunt',
        100 => 'Cameltoe',
        101 => 'Fingering',
        102 => 'Insertion',
        103 => 'Clit',
        104 => 'Squirt',
        105 => 'Fingering',
        106 => 'Fisting',
        107 => 'Blowjob',
        108 => 'Gagging',
        109 => 'Suck',
        110 => 'Blowjob',
        111 => 'Sperm',
        112 => 'Cum',
        113 => 'Creampie',
        114 => 'Jizz',
        115 => 'Bukkake',
        116 => 'Pussy',
        117 => 'Tribadism',
        118 => 'handjob',
        119 => 'gago',
        120 => 'tanga',
        121 => 'tangina',
        122 => 'putangina',
        123 => 'pakshet',
        124 => 'pokpok',
        125 => 'puta',
        126 => 'tarantado',
        127 => 'kupal',
        128 => 'hudas',
        129 => 'letse',
        130 => 'hinayupak',
        131 => 'bobo',
        132 => 'hindot',
        133 => 'gaga',
        134 => 'tado',
        135 => 'titi',
        136 => 'talik',
        137 => 'puke',
        138 => 'pekpek',
        139 => 'pucha',
        140 => 'lintik',
        141 => 'chupaero',
        142 => 'chupa',
        143 => 'salsalero',
        144 => 'kantotero',
        145 => 'kantot',
        146 => 'puwit',
        147 => 'putragis',
        148 => 'tamod',
        149 => 'regla',
        150 => 'bulbol',
        151 => 'ulol',
        152 => 'dodo',
        153 => 'dede',
        154 => 'jakolin',
        155 => 'siraulo',
        156 => 'utot',
        157 => 'tae',
        158 => 'anak ka ng puta',
        159 => 'anak ng puta',
        160 => 'iyot',
        161 => 'Buwa ka ng ina mo',
        162 => 'pucha',
        163 => 'jakol',
        164 => 'libog',
        165 => 'putatching',
        166 => 'tsupa',
        167 => 'bilat ina mo',
        168 => 'bayag',
        169 => 'kiki',
        170 => 'punyeta',
        171 => 'utong',
        172 => 'burat',
        173 => 'tite',
        174 => 'inutil',
        175 => 'hampaslupa',
        176 => 'puki',
        177 => 'betlog',
        178 => 'kantutero',
        179 => 'ugok'
    );


    public static function ngword_filter($text)
    {
        $ng_words = self::$ngwords;

        $text = ' '. $text. ' ';
        $text = str_replace(array("\n", "\r"), ' ', $text);
        $matches = array();
        $found = array();

        $matchFound = preg_match_all(
            "/\b(" . implode($ng_words,"|") . ")\b/i",
            $text,
            $found
        );


        if ($matchFound) {
            $words = array_unique($found[0]);
            foreach($words as $word) {
                $matches[] = $word;
            }
        }


        /*
        foreach($ng_words as $ng_word) {
            if(preg_match("/\b". $ng_word. "\b/i", $text)) {
                $matches[] = $ng_word;
            }
        }
        */
        return $matches;
    }

}