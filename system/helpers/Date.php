<?php

namespace Helper;

class Date
{
    /**
	 * возвращает день недели
	 * @param int $wd
	 * @return string
	 */
	public static function weekDay($wd)
	{
		$wDays = [1 => 'понедельник','вторник','среда','четверг','пятница','суббота','воскресенье'];
		return isset($wDays[$wd]) ? $wDays[$wd] : false;
	}

	/**
	 * возвращает день недели в винительном падеже
	 * @param int $wd
	 * @return string
	 */
	public static function weekDayAccusative($wd)
	{
		$wDays = [1 => 'понедельник','вторник','среду','четверг','пятницу','субботу','воскресенье'];
		return isset($wDays[$wd]) ? $wDays[$wd] : false;
	}

	/**
	 * возвращает название месяца
	 * @param int $month
	 * @return string
	 */
	public static function monthName($month)
	{
		$month = (int)$month;
		$months = [1 => 'январь','февраль','март','апрель','май','июнь','июль','август','сентябрь','октябрь','ноябрь','декабрь'];
		return isset($months[$month]) ? $months[$month] : false;
	}

	/**
	 * возвращает название месяца в родительном падеже
	 * @param int $month
	 * @return string
	 */
	public static function monthNameGenitive($month)
	{
		$month = (int)$month;
		$months = [1 => 'января','февраля','марта','апреля','мая','июня','июля','августа','сентября','октября','ноября','декабря'];
		return isset($months[$month]) ? $months[$month] : false;
	}
}