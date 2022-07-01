<?php

namespace Helper;

class Text
{
	/**
	 *
	 * @param string $pattern
	 * @param array $variables
	 * @return string
	 */
	public static function format($pattern, array $variables)
	{
		$result = $pattern;

		$keys = [];
		foreach (array_keys($variables) as $key) {
			$keys[] = '{' . $key . '}';
		}
		if (0 < count($keys)) {
			$result = str_replace($keys, $variables, $pattern);
		}

		return $result;
	}

	/**
	 * убирает из строки блоки [] внутри которых есть {} и сами []
	 * @param string $str
	 * @return string
	 */
	public static function removeUnusedOptionals($str) {
		$matches = [];
		preg_match_all('/(\[[\r\n]*.*?\])/', $str, $matches);
		if (isset($matches[0]))
		foreach ($matches[0] as $m) {
			$match = preg_match('/\{.*?\}/', $m);
			if ($match) $str = str_replace($m, '', $str);
		}
		$str = preg_replace('/[\[|\]]/', '', $str);
		return $str;
	}

	/**
	 * возвращает строку в транслите
	 * @param string $string
	 * @param string $sp - separator, '-' as default
	 * @return string
	 */
	public static function translit($string, $sp = '_')
	{
		return trim(preg_replace('~['.$sp.']{2,}~i', $sp, strtr($string, [
			//Кириллица
			"а" => "a", "б" => "b", "в" => "v", "г" => "g", "д" => "d", "е" => "e", "ж" => "zh", "з" => "z", "и" => "i", "й" => "y", "к" => "k", "л" => "l",
			"м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r", "с" => "s", "т" => "t", "у" => "u", "ф" => "f", "х" => "h", "ц" => "c", "ч" => "ch",
			"ш" => "sh", "щ" => "sch", "ъ" => "", "ы" => "y", "ь" => "", "э" => "e", "ю" => "yu", "я" => "ya", "ё" => "e",
			"А" => "A", "Б" => "B", "В" => "V", "Г" => "G", "Д" => "D", "Е" => "E", "Ж" => "ZH", "З" => "Z", "И" => "I", "Й" => "Y", "К" => "K", "Л" => "L",
			"М" => "M", "Н" => "N", "О" => "O", "П" => "P", "Р" => "R", "С" => "S", "Т" => "T", "У" => "U", "Ф" => "F", "Х" => "H", "Ц" => "C", "Ч" => "CH",
			"Ш" => "SH", "Щ" => "SCH", "Ъ" => "", "Ы" => "Y", "Ь" => "", "Э" => "E", "Ю" => "YU", "Я" => "YA", "Ё" => "E",
			//Убрать символы
			"," => "", "'" => "", "\"" => "", ")" => "", "(" => "", "!" => "", "?" => "", ">" => "", "<" => "", "~" => "", "@" => "", "#" => "", "№" => "",
			"$" => "", "%" => "", "^" => "", "&" => "", "*" => "", "+" => "", "=" => "", "{" => "", "}" => "", "[" => "", "]" => "", ":" => "", ";" => "", "`" => "",
			//Заменить на разделитель
			" " => $sp, "/" => $sp, "|" => $sp, "–" => $sp, "—" => $sp, "\\" => $sp,
		])), $sp);
	}

	/**
	 * возвращает строку в транслите
	 * @param string $string
	 * @param string $sp - separator, '-' as default
	 * @return string
	 */
	public static function translitUrl($string, $sp = '-')
	{
		return trim(strtolower(preg_replace('~['.$sp.']{2,}~i', $sp, strtr($string, [
			//Кириллица
			"а" => "a", "б" => "b", "в" => "v", "г" => "g", "д" => "d", "е" => "e", "ж" => "zh", "з" => "z", "и" => "i", "й" => "y", "к" => "k", "л" => "l",
			"м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r", "с" => "s", "т" => "t", "у" => "u", "ф" => "f", "х" => "h", "ц" => "c", "ч" => "ch",
			"ш" => "sh", "щ" => "sch", "ъ" => "", "ы" => "y", "ь" => "", "э" => "e", "ю" => "yu", "я" => "ya", "ё" => "e",
			"А" => "a", "Б" => "b", "В" => "v", "Г" => "g", "Д" => "d", "Е" => "e", "Ж" => "zh", "З" => "z", "И" => "i", "Й" => "y", "К" => "k", "Л" => "l",
			"М" => "m", "Н" => "n", "О" => "o", "П" => "p", "Р" => "r", "С" => "s", "Т" => "t", "У" => "u", "Ф" => "f", "Х" => "h", "Ц" => "c", "Ч" => "ch",
			"Ш" => "sh", "Щ" => "sch", "Ъ" => "", "Ы" => "y", "Ь" => "", "Э" => "e", "Ю" => "yu", "Я" => "ya", "Ё" => "e",
			//Убрать символы
			"," => "", "'" => "", "\"" => "", "." => "", ")" => "", "(" => "", "!" => "", "?" => "", ">" => "", "<" => "", "~" => "", "@" => "", "#" => "", "№" => "",
			"$" => "", "%" => "", "^" => "", "&" => "", "*" => "", "+" => "", "=" => "", "{" => "", "}" => "", "[" => "", "]" => "", ":" => "", ";" => "", "`" => "",
			//Заменить на разделитель
			" " => $sp, "/" => $sp, "|" => $sp, "–" => $sp, "—" => $sp, "_" => $sp, "\\" => $sp,
		]))), $sp);
	}

	/**
	 * Заменяет HEX на UTF-8
	 * @param string $string
	 * @return string
	 */
	public static function hex2utf($string)
	{
		return strtr($string, [
			"\u2116" => "№",
			"\u0430" => "а", "\u0431" => "б", "\u0432" => "в", "\u0433" => "г", "\u0434" => "д", "\u0435" => "е", "\u0451" => "ё", "\u0436" => "ж", "\u0437" => "з",
			"\u0438" => "и", "\u0439" => "й", "\u043a" => "к", "\u043b" => "л", "\u043c" => "м", "\u043d" => "н", "\u043e" => "о", "\u043f" => "п", "\u0440" => "р",
			"\u0441" => "с", "\u0442" => "т", "\u0443" => "у", "\u0444" => "ф", "\u0445" => "х", "\u0446" => "ц", "\u0447" => "ч", "\u0448" => "ш", "\u0449" => "щ",
			"\u044a" => "ъ", "\u044b" => "ы", "\u044c" => "ь", "\u044d" => "э", "\u044e" => "ю", "\u044f" => "я",
			"\u0410" => "А", "\u0411" => "Б", "\u0412" => "В", "\u0413" => "Г", "\u0414" => "Д", "\u0415" => "Е", "\u0401" => "Ё", "\u0416" => "Ж", "\u0417" => "З",
			"\u0418" => "И", "\u0419" => "Й", "\u041a" => "К", "\u041b" => "Л", "\u041c" => "М", "\u041d" => "Н", "\u041e" => "О", "\u041f" => "П", "\u0420" => "Р",
			"\u0421" => "С", "\u0422" => "Т", "\u0423" => "У", "\u0424" => "Ф", "\u0425" => "Х", "\u0426" => "Ц", "\u0427" => "Ч", "\u0428" => "Ш", "\u0429" => "Щ",
			"\u042a" => "Ъ", "\u042b" => "Ы", "\u042c" => "Ь", "\u042d" => "Э", "\u042e" => "Ю", "\u042f" => "Я",
		]);
	}

	/**
	 * Склонение существительного, в зависимости от кол-ва
	 * @param int $count
	 * @param string $one One variant
	 * @param string $few Few variant
	 * @param string $many Many variant
	 * @return string
	 */
	public static function getEnding($count = 0, $one = '', $few = '', $many = '')
	{
		$count = (int)abs($count);
		if ($count % 100 == 1 || ($count % 100 > 20) && ($count % 10 == 1 )) return $one;
		if ($count % 100 == 2 || ($count % 100 > 20) && ($count % 10 == 2 )) return $few;
		if ($count % 100 == 3 || ($count % 100 > 20) && ($count % 10 == 3 )) return $few;
		if ($count % 100 == 4 || ($count % 100 > 20) && ($count % 10 == 4 )) return $few;
		return $many;
	}

	/**
	 * Uppercase first symbol
	 * @param string $string
	 * @param string $encoding
	 * @return string
	 */
	public static function ucfirst($string, $encoding = 'UTF-8')
	{
		if (function_exists('mb_strtoupper') && function_exists('mb_substr') && !empty($string))
			return mb_strtoupper(mb_substr($string, 0, 1, $encoding), $encoding) . mb_substr($string, 1, mb_strlen($string, $encoding) - 1, $encoding);
		else
			return ucfirst($string);
	}

	/**
	 * заменяет html тег перевода строки <br /> на символ перевода каретки \n
	 * @param string $string
	 * @return string
	 */
	public static function br2nl($string)
	{
		return preg_replace('/\<br(\s*)?\/?\>/i', "\n", $string);
	}

	/**
	 * проверяет, является ли строка email адресом
	 * @param string $email
	 * @return boolean
	 */
	public static function isValidEmail($email)
	{
		return preg_match("'^[-\w_\.]+@(.*)\.[a-zA-Z]{2,6}$'", $email);
	}

	/**
	 * Смена раскладки клавиатуры
	 * @param string $str
	 * @return string
	 */
	public static function layoutLatToCyr($str, $reverse = false) {
		$l2c = array(
			"f" => "а",     "," => "б",     "d" => "в",     "u" => "г",     "l" => "д",     "t" => "е",     "`" => "е",
			";" => "ж",     "p" => "з",     "b" => "и",     "q" => "й",     "r" => "к",     "k" => "л",     "v" => "м",
			"y" => "н",     "j" => "о",     "g" => "п",     "h" => "р",     "c" => "с",     "n" => "т",     "e" => "у",
			"a" => "ф",     "[" => "х",     "w" => "ц",     "x" => "ч",     "i" => "ш",     "o" => "щ",     "]" => "ъ",
			"s" => "ы",     "m" => "ь",     "'" => "э",     "." => "ю",     "z" => "я",

			"F" => "А",     "<" => "Б",     "D" => "В",     "U" => "Г",     "L" => "Д",     "T" => "Е",     "~" => "Е",
			":" => "Ж",     "P" => "З",     "B" => "И",     "Q" => "Й",     "R" => "К",     "K" => "Л",     "V" => "М",
			"Y" => "Н",     "J" => "О",     "G" => "П",     "H" => "Р",     "C" => "С",     "N" => "Т",     "E" => "У",
			"A" => "Ф",     "{" => "Х",     "W" => "Ц",     "X" => "Ч",     "I" => "Ш",     "O" => "Щ",     "}" => "Ъ",
			"S" => "Ы",     "M" => "Ь",     "\"" => "Э",    ">" => "Ю",     "Z" => "Я",
		);

		return strtr($str, $reverse ? array_flip($l2c) : $l2c);
	}

	public static function wrongSimbols($string, $pattern = 'a-z0-9_')
	{
		$wrongSimbols = [];
		for($i = 0; $i < mb_strlen($string); $i++) {
			$simbol = mb_substr($string, $i, 1);
			if (!preg_match("/^[" . $pattern . "]+$/i", $simbol))
				$wrongSimbols[$simbol] = $simbol;
		}
		return $wrongSimbols;
	}

	/**
	 * @param string
	 * @return boolean
	 */
	public static function isJson($string)
	{
		if (is_string($string) == false) return false;
		json_decode($string, true);
		return (json_last_error() == JSON_ERROR_NONE);
	}

	/**
	 * Замена части строки маской
	 * @param string
	 * @return string
	 */
	public static function mask($str = '')
	{
		$len = mb_strlen($str);
		$allowedSimbols = [0, 1, 2, 3, $len - 4, $len - 3, $len - 2, $len - 1];
		$masked = '';
		for ($i = 0; $i < $len; $i++)
			$masked .= in_array($i, $allowedSimbols) ? mb_substr($str, $i, 1) : '*';
		return $masked;
	}

	/**
	 * Преобразует номер телефона в нужный формат
	 * @param string $phone +79998887766 или +380112223344
	 * @return string +7 (999) 888-77-66 или +380 (62) 253-37-82
	 */
	public static function phoneView($phone = '')
	{
		$phone = preg_replace('~\D~', '', $phone);
		switch (true) {
			//Russia
			case ((strlen($phone) == 11) && (strpos($phone, '7') === 0)):
				$format = [1 => '-', 3 => '-', 6 => ' )', 9 => '( '];
			break;
			//Ukraine
			case ((strlen($phone) == 12) && (strpos($phone, '380') === 0)):
				$format = [1 => '-', 3 => '-', 6 => ' )', 8 => '( '];
			break;
			default:
				return $phone;
		}

		/** номер позиции с конца (начиная с 0) => символ */
		$modify_phone = [];
		foreach(str_split(strrev($phone)) as $index => $simbol)
			$modify_phone[] = $simbol.(array_key_exists($index, $format)?$format[$index]:'');
		return '+'.strrev(implode('', $modify_phone));
	}

	/**
	 * Нормализует номер телефона
	 * @param string $phone +79998887766 / 79998887766 / 89998887766 / 9998887766
	 * @param string $countryCode
	 * @return string 79998887766
	 */
	public static function phoneNormalize($phone = '', $countryCode = '7')
	{
		$phone = preg_replace('~\D~', '', $phone);
		$len = strlen($phone);
		switch ($countryCode){
			case '7': //Russia
				if($len < 10)//
					return $phone;
				$phone = $countryCode.substr($phone, -10, 10);
			break;
		}
		return $phone;
	}

	/**
	 * @param string
	 * @return boolean
	 */
	public static function isValidPhone($phone, $countryCode = 7, $length = 11)
	{
		$phone = self::phoneNormalize($phone, $countryCode = 7);
		return strlen($phone) == $length;
	}

	/**
	 * @param string
	 * @return boolean
	 */
	public static function num2str($num)
	{
		$nul='ноль';
		$ten = [
			['','один','два','три','четыре','пять','шесть','семь','восемь','девять'],
			['','одна','две','три','четыре','пять','шесть','семь','восемь','девять'],
		];
		$a20 = ['десять','одиннадцать','двенадцать','тринадцать','четырнадцать','пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать'];
		$tens = [2 => 'двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят','восемьдесят','девяносто'];
		$hundred = ['','сто','двести','триста','четыреста','пятьсот','шестьсот','семьсот','восемьсот','девятьсот'];
		$unit = [ // Units
			['копейка','копейки','копеек',1],
			['рубль','рубля','рублей',0],
			['тысяча','тысячи','тысяч',1],
			['миллион','миллиона','миллионов',0],
			['миллиард','милиарда','миллиардов',0],
		];

		list($rub, $kop) = explode('.',sprintf("%015.2f", floatval($num)));
		$out = [];
		if (intval($rub)>0) {
			foreach(str_split($rub,3) as $uk=>$v) {
				if (!intval($v)) continue;
				$uk = sizeof($unit)-$uk-1; // unit key
				$gender = $unit[$uk][3];
				list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
				// mega-logic
				$out[] = $hundred[$i1]; # 1xx-9xx
				if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
				else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
				// units without rub & kop
				if ($uk>1) $out[]= self::_morph($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
			}
		} else $out[] = $nul;
		$out[] = self::_morph(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]); // rub
		$out[] = $kop.' '.self::_morph($kop,$unit[0][0],$unit[0][1],$unit[0][2]); // kop
		return trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
	}

    private static function _morph($n, $f1, $f2, $f5)
	{
        $n = abs(intval($n)) % 100;
        if ($n>10 && $n<20) return $f5;
        $n = $n % 10;
        if ($n>1 && $n<5) return $f2;
        if ($n==1) return $f1;
        return $f5;
    }

	/**
     * @return string
     */
    public static function generateUUID()
    {
		return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			mt_rand(0, 0xffff), mt_rand(0, 0xffff),
			mt_rand(0, 0xffff),
			mt_rand(0, 0x0fff) | 0x4000,
			mt_rand(0, 0x3fff) | 0x8000,
			mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
		);
    }

	/**
	 * @param int $length 10
	 * @param string $case mix, low, up
	 * @param boolean $pretty Without simbols [10Il1O]
	 * @return string
	 */
	public static function randomString($length = 10, $case = 'mix', $pretty = false)
	{
		// Набор цифр
		$numSet = $pretty ? '23456789' : '0123456789';

		// Набор символов UpperCase
		$upSet = in_array($case, ['mix', 'up']) ? ($pretty ? 'ABCDEFGHJKMNPQRSTUVWXYZ' : 'ABCDEFGHIJKLMNOPQRSTUVWXYZ') : '';

		// Набор символов LowerCase
		$lowSet = in_array($case, ['mix', 'low']) ? ($pretty ? 'abcdefghjkmnopqrstuvwxyz' : 'abcdefghijklmnopqrstuvwxyz') : '';

		$random = '';
		for ($i=0; $i<$length; $i++) {
			$random .= substr(str_shuffle("{$numSet}{$upSet}{$lowSet}"), 0, 1);
		}

		return $random;
	}
}
