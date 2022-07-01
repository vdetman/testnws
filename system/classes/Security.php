<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

class Security
{
	//Random Hash for protecting URLs
	protected $_xss_hash = '';

	//List of never allowed strings
	protected $_never_allowed_str = array(
		'document.cookie'	=> '[removed]',
		'document.write'	=> '[removed]',
		'.parentNode'		=> '[removed]',
		'.innerHTML'		=> '[removed]',
		'window.location'	=> '[removed]',
		'-moz-binding'		=> '[removed]',
		'<!--'				=> '&lt;!--',
		'-->'				=> '--&gt;',
		'<![CDATA['			=> '&lt;![CDATA[',
		'<comment>'			=> '&lt;comment&gt;'
	);

	//List of never allowed regex replacement
	protected $_never_allowed_regex = array(
		'javascript\s*:',
		'expression\s*(\(|&\#40;)', // CSS and IE
		'vbscript\s*:', //IE, surprise!
		'Redirect\s+302',
		"([\"'])?data\s*:[^\\1]*?base64[^\\1]*?,[^\\1]*?\\1?"
	);

	public function __construct(){}

	//XSS Clean
	public function xss_clean($str)
	{
		if(is_array($str)){
			while (list($key) = each($str)){
				$str[$key] = $this->xss_clean($str[$key]);
			}
			return $str;
		}

		//Remove Invisible Characters
		$str = $this->_remove_invisible_characters($str);

		// Validate Entities in URLs
		$str = $this->_validate_entities($str);

		//URL Decode
		$str = rawurldecode($str);

		//Convert character entities to ASCII
		$str = preg_replace_callback("/[a-z]+=([\'\"]).*?\\1/si", array($this, '_convert_attribute'), $str);
		$str = preg_replace_callback("/<\w+.*?(?=>|<|$)/si", array($this, '_decode_entity'), $str);

		//Remove Invisible Characters Again!
		$str = $this->_remove_invisible_characters($str);
		
		//Convert all tabs to spaces
		$str = strpos($str, "\t") !== FALSE ? str_replace("\t", ' ', $str) : $str;

		//Remove Strings that are never allowed
		$str = $this->_do_never_allowed($str);

		//Makes PHP tags safe
		$str = str_replace(array('<?', '?'.'>'), array('&lt;?', '?&gt;'), $str);
		
		//Compact any exploded words
		$words = array(
			'javascript', 'expression', 'vbscript', 'script', 'base64',
			'applet', 'alert', 'document', 'write', 'cookie', 'window'
		);
		foreach ($words as $word){
			$temp = '';
			for($i = 0, $wordlen = strlen($word); $i < $wordlen; $i++){
				$temp .= substr($word, $i, 1)."\s*";
			}
			$str = preg_replace_callback('#('.substr($temp, 0, -3).')(\W)#is', array($this, '_compact_exploded_words'), $str);
		}

		do{
			$original = $str;
			if (preg_match("/<a/i", $str))
				$str = preg_replace_callback("#<a\s+([^>]*?)(>|$)#si", array($this, '_js_link_removal'), $str);

			if (preg_match("/<img/i", $str))
				$str = preg_replace_callback("#<img\s+([^>]*?)(\s?/?>|$)#si", array($this, '_js_img_removal'), $str);

			if (preg_match("/script/i", $str) || preg_match("/xss/i", $str))
				$str = preg_replace("#<(/*)(script|xss)(.*?)\>#si", '[removed]', $str);
		}
		while($original != $str);
		unset($original);

		// Remove evil attributes such as style, onclick and xmlns
		$str = $this->_remove_evil_attributes($str);

		//Sanitize naughty HTML elements
		$naughty = 'alert|applet|audio|basefont|base|behavior|bgsound|blink|body|embed|expression|form|frameset|frame|head|html|ilayer|iframe|input|isindex|layer|link|meta|object|plaintext|style|script|textarea|title|video|xml|xss';
		$str = preg_replace_callback('#<(/*\s*)('.$naughty.')([^><]*)([><]*)#is', array($this, '_sanitize_naughty_html'), $str);

		//Sanitize naughty scripting elements
		$str = preg_replace('#(alert|cmd|passthru|eval|exec|expression|system|fopen|fsockopen|file|file_get_contents|readfile|unlink)(\s*)\((.*?)\)#si', "\\1\\2&#40;\\3&#41;", $str);

		// Final clean up
		$str = $this->_do_never_allowed($str);

		return $str;
	}
	
	protected function _remove_invisible_characters($str, $url_encoded = true)
	{
		$non_displayables = [];
		
		// every control character except newline (dec 10)
		// carriage return (dec 13), and horizontal tab (dec 09)
		
		if ($url_encoded){
			$non_displayables[] = '/%0[0-8bcef]/';	// url encoded 00-08, 11, 12, 14, 15
			$non_displayables[] = '/%1[0-9a-f]/';	// url encoded 16-31
		}
		
		$non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';	// 00-08, 11, 12, 14-31, 127

		do{
			$str = preg_replace($non_displayables, '', $str, -1, $count);
		}
		while ($count);
		
		return $str;
	}
	
	//Compact Exploded Words
	protected function _compact_exploded_words($matches)
	{
		return preg_replace('/\s+/s', '', $matches[1]).$matches[2];
	}

	//Remove Evil HTML Attributes (like evenhandlers and style)
	protected function _remove_evil_attributes($str)
	{
		// All javascript event handlers (e.g. onload, onclick, onmouseover), style, and xmlns
		$evil_attributes = array('on\w*', 'style', 'xmlns', 'formaction');

		do {
			$count = 0;
			$attribs = [];

			// find occurrences of illegal attribute strings with quotes (042 and 047 are octal quotes)
			preg_match_all('/('.implode('|', $evil_attributes).')\s*=\s*(\042|\047)([^\\2]*?)(\\2)/is', $str, $matches, PREG_SET_ORDER);

			foreach ($matches as $attr)
				$attribs[] = preg_quote($attr[0], '/');

			// find occurrences of illegal attribute strings without quotes
			preg_match_all('/('.implode('|', $evil_attributes).')\s*=\s*([^\s>]*)/is', $str, $matches, PREG_SET_ORDER);

			foreach ($matches as $attr)
				$attribs[] = preg_quote($attr[0], '/');

			// replace illegal attribute strings that are inside an html tag
			if (count($attribs) > 0)
				$str = preg_replace('/(<?)(\/?[^><]+?)([^A-Za-z<>\-])(.*?)('.implode('|', $attribs).')(.*?)([\s><]?)([><]*)/i', '$1$2 $4$6$7$8', $str, -1, $count);

		}while($count);

		return $str;
	}
	
	//HTML Entities Decode
	public function entity_decode($str, $charset='UTF-8')
	{
		if (stristr($str, '&') === false)
			return $str;

		$str = html_entity_decode($str, ENT_COMPAT, $charset);
		$str = preg_replace('~&#x(0*[0-9a-f]{2,5})~ei', 'chr(hexdec("\\1"))', $str);
		return preg_replace('~&#([0-9]{2,4})~e', 'chr(\\1)', $str);
	}
	
	//Random Hash for protecting URLs
	public function xss_hash()
	{
		if ($this->_xss_hash == ''){
			mt_srand();
			$this->_xss_hash = md5(time() + mt_rand(0, 1999999999));
		}
		return $this->_xss_hash;
	}
	
	//Sanitize Naughty HTML
	protected function _sanitize_naughty_html($matches)
	{
		// encode opening brace
		$str = '&lt;'.$matches[1].$matches[2].$matches[3];

		// encode captured opening or closing brace to prevent recursive vectors
		$str .= str_replace(array('>', '<'), array('&gt;', '&lt;'), $matches[4]);

		return $str;
	}

	//JS Link Removal
	protected function _js_link_removal($match)
	{
		return str_replace(
			$match[1],
			preg_replace(
				'#href=.*?(alert\(|alert&\#40;|javascript\:|livescript\:|mocha\:|charset\=|window\.|document\.|\.cookie|<script|<xss|data\s*:)#si',
				'',
				$this->_filter_attributes(str_replace(array('<', '>'), '', $match[1]))
			),
			$match[0]
		);
	}

	//JS Image Removal
	protected function _js_img_removal($match)
	{
		return str_replace(
			$match[1],
			preg_replace(
				'#src=.*?(alert\(|alert&\#40;|javascript\:|livescript\:|mocha\:|charset\=|window\.|document\.|\.cookie|<script|<xss|base64\s*,)#si',
				'',
				$this->_filter_attributes(str_replace(array('<', '>'), '', $match[1]))
			),
			$match[0]
		);
	}

	//Attribute Conversion
	protected function _convert_attribute($match)
	{
		return str_replace(array('>', '<', '\\'), array('&gt;', '&lt;', '\\\\'), $match[0]);
	}

	//Filter Attributes
	protected function _filter_attributes($str)
	{
		$out = '';
		if (preg_match_all('#\s*[a-z\-]+\s*=\s*(\042|\047)([^\\1]*?)\\1#is', $str, $matches))
			foreach ($matches[0] as $match)
				$out .= preg_replace("#/\*.*?\*/#s", '', $match);

		return $out;
	}

	//HTML Entity Decode Callback
	protected function _decode_entity($match)
	{
		return $this->entity_decode($match[0], strtoupper('utf-8'));
	}

	//Validate URL entities
	protected function _validate_entities($str)
	{
		//Protect GET variables in URLs
		$str = preg_replace('|\&([a-z\_0-9\-]+)\=([a-z\_0-9\-]+)|i', $this->xss_hash()."\\1=\\2", $str);

		//Validate standard character entities
		$str = preg_replace('#(&\#?[0-9a-z]{2,})([\x00-\x20])*;?#i', "\\1;\\2", $str);

		//Validate UTF16 two byte encoding (x00)
		$str = preg_replace('#(&\#x?)([0-9A-F]+);?#i',"\\1\\2;",$str);

		//Un-Protect GET variables in URLs
		$str = str_replace($this->xss_hash(), '&', $str);

		return $str;
	}

	//Do Never Allowed
	protected function _do_never_allowed($str)
	{
		$str = str_replace(array_keys($this->_never_allowed_str), $this->_never_allowed_str, $str);

		foreach ($this->_never_allowed_regex as $regex)
		{
			$str = preg_replace('#'.$regex.'#is', '[removed]', $str);
		}

		return $str;
	}
}