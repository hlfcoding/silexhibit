<?php if (!defined('SITE')) exit('No direct script access allowed');

function romanizeFile ()
{
    
    /**
     * Romanization lookup table
     *
     * This lookup tables provides a way to transform strings written in a language
     * different from the ones based upon latin letters into plain ASCII.
     *
     * Please note: this is not a scientific transliteration table. It only works
     * oneway from nonlatin to ASCII and it works by simple character replacement
     * only. Specialities of each language are not supported.
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     * @author Vitaly Blokhin <vitinfo@vitn.com>
     * @link   http://www.uconv.com/translit.htm
     * @author Bisqwit <bisqwit@iki.fi>
     * @link   http://kanjidict.stc.cx/hiragana.php?src=2
     * @link   http://www.translatum.gr/converter/greek-transliteration.htm
     * @link   http://en.wikipedia.org/wiki/Royal_Thai_General_System_of_Transcription
     * @link   http://www.btranslations.com/resources/romanization/korean.asp
     */
    
    $UTF8_ROMANIZATION = array(
      //russian cyrillic
      'а'=>'a','А'=>'A','б'=>'b','Б'=>'B','в'=>'v','В'=>'V','г'=>'g','Г'=>'G',
      'д'=>'d','Д'=>'D','е'=>'e','Е'=>'E','ё'=>'jo','Ё'=>'Jo','ж'=>'zh','Ж'=>'Zh',
      'з'=>'z','З'=>'Z','и'=>'i','И'=>'I','й'=>'j','Й'=>'J','к'=>'k','К'=>'K',
      'л'=>'l','Л'=>'L','м'=>'m','М'=>'M','н'=>'n','Н'=>'N','о'=>'o','О'=>'O',
      'п'=>'p','П'=>'P','р'=>'r','Р'=>'R','с'=>'s','С'=>'S','т'=>'t','Т'=>'T',
      'у'=>'u','У'=>'U','ф'=>'f','Ф'=>'F','х'=>'x','Х'=>'X','ц'=>'c','Ц'=>'C',
      'ч'=>'ch','Ч'=>'Ch','ш'=>'sh','Ш'=>'Sh','щ'=>'th','Щ'=>'Th','ъ'=>'qh',
      'Ъ'=>'Qh','ы'=>'y','Ы'=>'Y','ь'=>'q','Ь'=>'Q','э'=>'eh','Э'=>'Eh','ю'=>'ju',
      'Ю'=>'Ju','я'=>'ja','Я'=>'Ja',
      // Ukrainian cyrillic
      'Ґ'=>'Gh','ґ'=>'gh','Є'=>'Je','є'=>'je','І'=>'I','і'=>'i','Ї'=>'Ji','ї'=>'ji',
      // Georgian
      'ა'=>'a','ბ'=>'b','გ'=>'g','დ'=>'d','ე'=>'e','ვ'=>'v','ზ'=>'z','თ'=>'th',
      'ი'=>'i','კ'=>'p','ლ'=>'l','მ'=>'m','ნ'=>'n','ო'=>'o','პ'=>'p','ჟ'=>'zh',
      'რ'=>'r','ს'=>'s','ტ'=>'t','უ'=>'u','ფ'=>'ph','ქ'=>'kh','ღ'=>'gh','ყ'=>'q',
      'შ'=>'sh','ჩ'=>'ch','ც'=>'c','ძ'=>'dh','წ'=>'w','ჭ'=>'j','ხ'=>'x','ჯ'=>'jh',
      'ჰ'=>'xh',
      //Sanskrit
      'अ'=>'a','आ'=>'ah','इ'=>'i','ई'=>'ih','उ'=>'u','ऊ'=>'uh','ऋ'=>'ry',
      'ॠ'=>'ryh','ऌ'=>'ly','ॡ'=>'lyh','ए'=>'e','ऐ'=>'ay','ओ'=>'o','औ'=>'aw',
      'अं'=>'amh','अः'=>'aq','क'=>'k','ख'=>'kh','ग'=>'g','घ'=>'gh','ङ'=>'nh',
      'च'=>'c','छ'=>'ch','ज'=>'j','झ'=>'jh','ञ'=>'ny','ट'=>'tq','ठ'=>'tqh',
      'ड'=>'dq','ढ'=>'dqh','ण'=>'nq','त'=>'t','थ'=>'th','द'=>'d','ध'=>'dh',
      'न'=>'n','प'=>'p','फ'=>'ph','ब'=>'b','भ'=>'bh','म'=>'m','य'=>'z','र'=>'r',
      'ल'=>'l','व'=>'v','श'=>'sh','ष'=>'sqh','स'=>'s','ह'=>'x',
      //Hebrew
      'ב'=>'a','ג'=>'b','ד'=>'g','ה'=>'d','ו'=>'x','ז'=>'v','ח'=>'kh','ט'=>'th',
      'י'=>'y','ך'=>'k','כ'=>'k','ל'=>'l','ם'=>'m','מ'=>'m','ן'=>'n','נ'=>'n',
      'ס'=>'s','ע'=>'ah','ף'=>'p','פ'=>'p','ץ'=>'c','צ'=>'c','ק'=>'q','ר'=>'r',
      'ש'=>'sh','ת'=>'t',
      //Arabic
      'ا'=>'a','ب'=>'b','ت'=>'t','ث'=>'th','ج'=>'g','ح'=>'xh','خ'=>'x','د'=>'d',
      'ذ'=>'dh','ر'=>'r','ز'=>'z','س'=>'s','ش'=>'sh','ص'=>'s\'','ض'=>'d\'',
      'ط'=>'t\'','ظ'=>'z\'','ع'=>'y','غ'=>'gh','ف'=>'f','ق'=>'q','ك'=>'k',
      'ل'=>'l','م'=>'m','ن'=>'n','ه'=>'x\'','و'=>'u','ي'=>'i',

      // Japanese hiragana
      'あ'=>'a','え'=>'e','い'=>'i','お'=>'o','う'=>'u','ば'=>'ba','べ'=>'be',
      'び'=>'bi','ぼ'=>'bo','ぶ'=>'bu','し'=>'ci','だ'=>'da','で'=>'de','ぢ'=>'di',
      'ど'=>'do','づ'=>'du','ふぁ'=>'fa','ふぇ'=>'fe','ふぃ'=>'fi','ふぉ'=>'fo',
      'ふ'=>'fu','が'=>'ga','げ'=>'ge','ぎ'=>'gi','ご'=>'go','ぐ'=>'gu','は'=>'ha',
      'へ'=>'he','ひ'=>'hi','ほ'=>'ho','ふ'=>'hu','じゃ'=>'ja','じぇ'=>'je',
      'じ'=>'ji','じょ'=>'jo','じゅ'=>'ju','か'=>'ka','け'=>'ke','き'=>'ki',
      'こ'=>'ko','く'=>'ku','ら'=>'la','れ'=>'le','り'=>'li','ろ'=>'lo','る'=>'lu',
      'ま'=>'ma','め'=>'me','み'=>'mi','も'=>'mo','む'=>'mu','な'=>'na','ね'=>'ne',
      'に'=>'ni','の'=>'no','ぬ'=>'nu','ぱ'=>'pa','ぺ'=>'pe','ぴ'=>'pi','ぽ'=>'po',
      'ぷ'=>'pu','ら'=>'ra','れ'=>'re','り'=>'ri','ろ'=>'ro','る'=>'ru','さ'=>'sa',
      'せ'=>'se','し'=>'si','そ'=>'so','す'=>'su','た'=>'ta','て'=>'te','ち'=>'ti',
      'と'=>'to','つ'=>'tu','ヴぁ'=>'va','ヴぇ'=>'ve','ヴぃ'=>'vi','ヴぉ'=>'vo',
      'ヴ'=>'vu','わ'=>'wa','うぇ'=>'we','うぃ'=>'wi','を'=>'wo','や'=>'ya','いぇ'=>'ye',
      'い'=>'yi','よ'=>'yo','ゆ'=>'yu','ざ'=>'za','ぜ'=>'ze','じ'=>'zi','ぞ'=>'zo',
      'ず'=>'zu','びゃ'=>'bya','びぇ'=>'bye','びぃ'=>'byi','びょ'=>'byo','びゅ'=>'byu',
      'ちゃ'=>'cha','ちぇ'=>'che','ち'=>'chi','ちょ'=>'cho','ちゅ'=>'chu','ちゃ'=>'cya',
      'ちぇ'=>'cye','ちぃ'=>'cyi','ちょ'=>'cyo','ちゅ'=>'cyu','でゃ'=>'dha','でぇ'=>'dhe',
      'でぃ'=>'dhi','でょ'=>'dho','でゅ'=>'dhu','どぁ'=>'dwa','どぇ'=>'dwe','どぃ'=>'dwi',
      'どぉ'=>'dwo','どぅ'=>'dwu','ぢゃ'=>'dya','ぢぇ'=>'dye','ぢぃ'=>'dyi','ぢょ'=>'dyo',
      'ぢゅ'=>'dyu','ぢ'=>'dzi','ふぁ'=>'fwa','ふぇ'=>'fwe','ふぃ'=>'fwi','ふぉ'=>'fwo',
      'ふぅ'=>'fwu','ふゃ'=>'fya','ふぇ'=>'fye','ふぃ'=>'fyi','ふょ'=>'fyo','ふゅ'=>'fyu',
      'ぎゃ'=>'gya','ぎぇ'=>'gye','ぎぃ'=>'gyi','ぎょ'=>'gyo','ぎゅ'=>'gyu','ひゃ'=>'hya',
      'ひぇ'=>'hye','ひぃ'=>'hyi','ひょ'=>'hyo','ひゅ'=>'hyu','じゃ'=>'jya','じぇ'=>'jye',
      'じぃ'=>'jyi','じょ'=>'jyo','じゅ'=>'jyu','きゃ'=>'kya','きぇ'=>'kye','きぃ'=>'kyi',
      'きょ'=>'kyo','きゅ'=>'kyu','りゃ'=>'lya','りぇ'=>'lye','りぃ'=>'lyi','りょ'=>'lyo',
      'りゅ'=>'lyu','みゃ'=>'mya','みぇ'=>'mye','みぃ'=>'myi','みょ'=>'myo','みゅ'=>'myu',
      'ん'=>'n','にゃ'=>'nya','にぇ'=>'nye','にぃ'=>'nyi','にょ'=>'nyo','にゅ'=>'nyu',
      'ぴゃ'=>'pya','ぴぇ'=>'pye','ぴぃ'=>'pyi','ぴょ'=>'pyo','ぴゅ'=>'pyu','りゃ'=>'rya',
      'りぇ'=>'rye','りぃ'=>'ryi','りょ'=>'ryo','りゅ'=>'ryu','しゃ'=>'sha','しぇ'=>'she',
      'し'=>'shi','しょ'=>'sho','しゅ'=>'shu','すぁ'=>'swa','すぇ'=>'swe','すぃ'=>'swi',
      'すぉ'=>'swo','すぅ'=>'swu','しゃ'=>'sya','しぇ'=>'sye','しぃ'=>'syi','しょ'=>'syo',
      'しゅ'=>'syu','てゃ'=>'tha','てぇ'=>'the','てぃ'=>'thi','てょ'=>'tho','てゅ'=>'thu',
      'つゃ'=>'tsa','つぇ'=>'tse','つぃ'=>'tsi','つょ'=>'tso','つ'=>'tsu','とぁ'=>'twa',
      'とぇ'=>'twe','とぃ'=>'twi','とぉ'=>'two','とぅ'=>'twu','ちゃ'=>'tya','ちぇ'=>'tye',
      'ちぃ'=>'tyi','ちょ'=>'tyo','ちゅ'=>'tyu','ヴゃ'=>'vya','ヴぇ'=>'vye','ヴぃ'=>'vyi',
      'ヴょ'=>'vyo','ヴゅ'=>'vyu','うぁ'=>'wha','うぇ'=>'whe','うぃ'=>'whi','うぉ'=>'who',
      'うぅ'=>'whu','ゑ'=>'wye','ゐ'=>'wyi','じゃ'=>'zha','じぇ'=>'zhe','じぃ'=>'zhi',
      'じょ'=>'zho','じゅ'=>'zhu','じゃ'=>'zya','じぇ'=>'zye','じぃ'=>'zyi','じょ'=>'zyo',
      'じゅ'=>'zyu',
    
      // Japanese katakana
      'ア'=>'a','エ'=>'e','イ'=>'i','オ'=>'o','ウ'=>'u','バ'=>'ba','ベ'=>'be','ビ'=>'bi',
      'ボ'=>'bo','ブ'=>'bu','シ'=>'ci','ダ'=>'da','デ'=>'de','ヂ'=>'di','ド'=>'do',
      'ヅ'=>'du','ファ'=>'fa','フェ'=>'fe','フィ'=>'fi','フォ'=>'fo','フ'=>'fu','ガ'=>'ga',
      'ゲ'=>'ge','ギ'=>'gi','ゴ'=>'go','グ'=>'gu','ハ'=>'ha','ヘ'=>'he','ヒ'=>'hi','ホ'=>'ho',
      'フ'=>'hu','ジャ'=>'ja','ジェ'=>'je','ジ'=>'ji','ジョ'=>'jo','ジュ'=>'ju','カ'=>'ka',
      'ケ'=>'ke','キ'=>'ki','コ'=>'ko','ク'=>'ku','ラ'=>'la','レ'=>'le','リ'=>'li','ロ'=>'lo',
      'ル'=>'lu','マ'=>'ma','メ'=>'me','ミ'=>'mi','モ'=>'mo','ム'=>'mu','ナ'=>'na','ネ'=>'ne',
      'ニ'=>'ni','ノ'=>'no','ヌ'=>'nu','パ'=>'pa','ペ'=>'pe','ピ'=>'pi','ポ'=>'po','プ'=>'pu',
      'ラ'=>'ra','レ'=>'re','リ'=>'ri','ロ'=>'ro','ル'=>'ru','サ'=>'sa','セ'=>'se','シ'=>'si',
      'ソ'=>'so','ス'=>'su','タ'=>'ta','テ'=>'te','チ'=>'ti','ト'=>'to','ツ'=>'tu','ヴァ'=>'va',
      'ヴェ'=>'ve','ヴィ'=>'vi','ヴォ'=>'vo','ヴ'=>'vu','ワ'=>'wa','ウェ'=>'we','ウィ'=>'wi',
      'ヲ'=>'wo','ヤ'=>'ya','イェ'=>'ye','イ'=>'yi','ヨ'=>'yo','ユ'=>'yu','ザ'=>'za','ゼ'=>'ze',
      'ジ'=>'zi','ゾ'=>'zo','ズ'=>'zu','ビャ'=>'bya','ビェ'=>'bye','ビィ'=>'byi','ビョ'=>'byo',
      'ビュ'=>'byu','チャ'=>'cha','チェ'=>'che','チ'=>'chi','チョ'=>'cho','チュ'=>'chu',
      'チャ'=>'cya','チェ'=>'cye','チィ'=>'cyi','チョ'=>'cyo','チュ'=>'cyu','デャ'=>'dha',
      'デェ'=>'dhe','ディ'=>'dhi','デョ'=>'dho','デュ'=>'dhu','ドァ'=>'dwa','ドェ'=>'dwe',
      'ドィ'=>'dwi','ドォ'=>'dwo','ドゥ'=>'dwu','ヂャ'=>'dya','ヂェ'=>'dye','ヂィ'=>'dyi',
      'ヂョ'=>'dyo','ヂュ'=>'dyu','ヂ'=>'dzi','ファ'=>'fwa','フェ'=>'fwe','フィ'=>'fwi',
      'フォ'=>'fwo','フゥ'=>'fwu','フャ'=>'fya','フェ'=>'fye','フィ'=>'fyi','フョ'=>'fyo',
      'フュ'=>'fyu','ギャ'=>'gya','ギェ'=>'gye','ギィ'=>'gyi','ギョ'=>'gyo','ギュ'=>'gyu',
      'ヒャ'=>'hya','ヒェ'=>'hye','ヒィ'=>'hyi','ヒョ'=>'hyo','ヒュ'=>'hyu','ジャ'=>'jya',
      'ジェ'=>'jye','ジィ'=>'jyi','ジョ'=>'jyo','ジュ'=>'jyu','キャ'=>'kya','キェ'=>'kye',
      'キィ'=>'kyi','キョ'=>'kyo','キュ'=>'kyu','リャ'=>'lya','リェ'=>'lye','リィ'=>'lyi',
      'リョ'=>'lyo','リュ'=>'lyu','ミャ'=>'mya','ミェ'=>'mye','ミィ'=>'myi','ミョ'=>'myo',
      'ミュ'=>'myu','ン'=>'n','ニャ'=>'nya','ニェ'=>'nye','ニィ'=>'nyi','ニョ'=>'nyo',
      'ニュ'=>'nyu','ピャ'=>'pya','ピェ'=>'pye','ピィ'=>'pyi','ピョ'=>'pyo','ピュ'=>'pyu',
      'リャ'=>'rya','リェ'=>'rye','リィ'=>'ryi','リョ'=>'ryo','リュ'=>'ryu','シャ'=>'sha',
      'シェ'=>'she','シ'=>'shi','ショ'=>'sho','シュ'=>'shu','スァ'=>'swa','スェ'=>'swe',
      'スィ'=>'swi','スォ'=>'swo','スゥ'=>'swu','シャ'=>'sya','シェ'=>'sye','シィ'=>'syi',
      'ショ'=>'syo','シュ'=>'syu','テャ'=>'tha','テェ'=>'the','ティ'=>'thi','テョ'=>'tho',
      'テュ'=>'thu','ツャ'=>'tsa','ツェ'=>'tse','ツィ'=>'tsi','ツョ'=>'tso','ツ'=>'tsu',
      'トァ'=>'twa','トェ'=>'twe','トィ'=>'twi','トォ'=>'two','トゥ'=>'twu','チャ'=>'tya',
      'チェ'=>'tye','チィ'=>'tyi','チョ'=>'tyo','チュ'=>'tyu','ヴャ'=>'vya','ヴェ'=>'vye',
      'ヴィ'=>'vyi','ヴョ'=>'vyo','ヴュ'=>'vyu','ウァ'=>'wha','ウェ'=>'whe','ウィ'=>'whi',
      'ウォ'=>'who','ウゥ'=>'whu','ヱ'=>'wye','ヰ'=>'wyi','ジャ'=>'zha','ジェ'=>'zhe',
      'ジィ'=>'zhi','ジョ'=>'zho','ジュ'=>'zhu','ジャ'=>'zya','ジェ'=>'zye','ジィ'=>'zyi',
      'ジョ'=>'zyo','ジュ'=>'zyu',

      // "Greeklish"
      'Γ'=>'G','Δ'=>'E','Θ'=>'Th','Λ'=>'L','Ξ'=>'X','Π'=>'P','Σ'=>'S','Φ'=>'F','Ψ'=>'Ps',
      'γ'=>'g','δ'=>'e','θ'=>'th','λ'=>'l','ξ'=>'x','π'=>'p','σ'=>'s','φ'=>'f','ψ'=>'ps',

      // Thai
      'ก'=>'k','ข'=>'kh','ฃ'=>'kh','ค'=>'kh','ฅ'=>'kh','ฆ'=>'kh','ง'=>'ng','จ'=>'ch',
      'ฉ'=>'ch','ช'=>'ch','ซ'=>'s','ฌ'=>'ch','ญ'=>'y','ฎ'=>'d','ฏ'=>'t','ฐ'=>'th',
      'ฑ'=>'d','ฒ'=>'th','ณ'=>'n','ด'=>'d','ต'=>'t','ถ'=>'th','ท'=>'th','ธ'=>'th',
      'น'=>'n','บ'=>'b','ป'=>'p','ผ'=>'ph','ฝ'=>'f','พ'=>'ph','ฟ'=>'f','ภ'=>'ph',
      'ม'=>'m','ย'=>'y','ร'=>'r','ฤ'=>'rue','ฤๅ'=>'rue','ล'=>'l','ฦ'=>'lue',
      'ฦๅ'=>'lue','ว'=>'w','ศ'=>'s','ษ'=>'s','ส'=>'s','ห'=>'h','ฬ'=>'l','ฮ'=>'h',
      'ะ'=>'a','–ั'=>'a','รร'=>'a','า'=>'a','รร'=>'an','ำ'=>'am','–ิ'=>'i','–ี'=>'i',
      '–ึ'=>'ue','–ื'=>'ue','–ุ'=>'u','–ู'=>'u','เะ'=>'e','เ–็'=>'e','เ'=>'e','แะ'=>'ae',
      'แ'=>'ae','โะ'=>'o','โ'=>'o','เาะ'=>'o','อ'=>'o','เอะ'=>'oe','เ–ิ'=>'oe',
      'เอ'=>'oe','เ–ียะ'=>'ia','เ–ีย'=>'ia','เ–ือะ'=>'uea','เ–ือ'=>'uea','–ัวะ'=>'ua',
      '–ัว'=>'ua','ว'=>'ua','ใ'=>'ai','ไ'=>'ai','–ัย'=>'ai','ไย'=>'ai','าย'=>'ai',
      'เา'=>'ao','าว'=>'ao','–ุย'=>'ui','โย'=>'oi','อย'=>'oi','เย'=>'oei','เ–ือย'=>'ueai',
      'วย'=>'uai','–ิว'=>'io','เ–็ว'=>'eo','เว'=>'eo','แ–็ว'=>'aeo','แว'=>'aeo',
      'เ–ียว'=>'iao',

      // Korean
      'ㄱ'=>'k','ㅋ'=>'kh','ㄲ'=>'kk','ㄷ'=>'t','ㅌ'=>'th','ㄸ'=>'tt','ㅂ'=>'p',
      'ㅍ'=>'ph','ㅃ'=>'pp','ㅈ'=>'c','ㅊ'=>'ch','ㅉ'=>'cc','ㅅ'=>'s','ㅆ'=>'ss',
      'ㅎ'=>'h','ㅇ'=>'ng','ㄴ'=>'n','ㄹ'=>'l','ㅁ'=>'m', 'ㅏ'=>'a','ㅓ'=>'e','ㅗ'=>'o',
      'ㅜ'=>'wu','ㅡ'=>'u','ㅣ'=>'i','ㅐ'=>'ay','ㅔ'=>'ey','ㅚ'=>'oy','ㅘ'=>'wa','ㅝ'=>'we',
      'ㅟ'=>'wi','ㅙ'=>'way','ㅞ'=>'wey','ㅢ'=>'uy','ㅑ'=>'ya','ㅕ'=>'ye','ㅛ'=>'oy',
      'ㅠ'=>'yu','ㅒ'=>'yay','ㅖ'=>'yey',
    );

    return $UTF8_ROMANIZATION;
}