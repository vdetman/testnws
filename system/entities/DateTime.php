<?php namespace Entity;

class DateTime extends \DateTime
{
	/**
	 * @param string $time
	 */
	public function __construct($time = null)
	{
		parent::__construct($time);
	}

	/**
	 * @param string $format
	 */
	public function format($format = false)
	{
		$format = $this->_parseFormat($format);
		return parent::format($format);
	}

	/**
	 * @param string $format
	 * @return string
	 */
	private function _parseFormat($format)
	{
		// Search human-understandable labels

		// #Hmg - месяц в родительном падеже
		if (false !== strpos($format, '#Hmg')) {
			$format = str_replace('#Hmg', $this->_addSlashes(\Helper\Date::monthNameGenitive(date('m', $this->getTimestamp()))), $format);
		}

		// #Hm - месяц в именительном падеже
		if (false !== strpos($format, '#Hm')) {
			$format = str_replace('#Hm', $this->_addSlashes(\Helper\Date::monthName(date('m', $this->getTimestamp()))), $format);
		}

		// #Hwa - день недели в винительном падеже
		if (false !== strpos($format, '#Hwa')) {
			$format = str_replace('#Hwa', $this->_addSlashes(\Helper\Date::weekDayAccusative(date('N', $this->getTimestamp()))), $format);
		}

		// #Hw - день недели в именительном падеже
		if (false !== strpos($format, '#Hw')) {
			$format = str_replace('#Hw', $this->_addSlashes(\Helper\Date::weekDay(date('N', $this->getTimestamp()))), $format);
		}

		// #Hd - дата в виде наречия
		if (false !== strpos($format, '#Hd')) {
			$dialect = '';
			if (date('d.m.Y', $this->getTimestamp()) == date('d.m.Y', strtotime("-2 days"))) {
				$dialect = 'позавчера';
			} else if (date('d.m.Y', $this->getTimestamp()) == date('d.m.Y', strtotime("-1 days"))) {
				$dialect = 'вчера';
			} else if (date('d.m.Y', $this->getTimestamp()) == date('d.m.Y')) {
				$dialect = 'сегодня';
			} else if (date('d.m.Y', $this->getTimestamp()) == date('d.m.Y', strtotime("+1 days"))) {
				$dialect = 'завтра';
			} else if (date('d.m.Y', $this->getTimestamp()) == date('d.m.Y', strtotime("+2 days"))) {
				$dialect = 'послезавтра';
			}
			$format = str_replace('#Hd', $this->_addSlashes($dialect), $format);
		}

		return $format;
	}

	/**
	 * @param string $string
	 * @return string
	 */
	private function _addSlashes($string)
	{
		$str = '';
		for ($i=0; $i<mb_strlen($string); $i++) {
			$str .= '\\' . mb_substr($string, $i, 1);
		}

		return $str;
	}
}