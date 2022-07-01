<?php namespace Helper;

class File
{
    /**
	 * @return boolean
	 */
	public static function array2Archive($data, $filePath, $level = 9)
	{
		$gzipFile = $filePath . '.gz';
		$gzipData = gzencode(implode("", $data), $level);
		file_put_contents($gzipFile, $gzipData);
		return is_file($gzipFile) ? $gzipFile : false;
	}
}