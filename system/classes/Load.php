<?php if(!defined('VF_ROOT_DIR')) die('Direct access denied');

class Load extends Core
{
	private static $instances = []; //Instances of loaded classes

	/**
	 * @param string $library
	 * @param string $directory
	 * @param string $className
	 * @return \className
	 */
	public static function library($library, $directory = '', $className = false)
	{
		$className = ucfirst($className ?: $library);
		$fileName = ucfirst($library);
		$libDirectoryName = '/libraries/'.($directory ? trim($directory, '/').'/' : '');

		if (array_key_exists($className, self::$instances)) {
			return self::$instances[$className];
		}

		$sourceDirectories = [
			VF_APP_DIR . $libDirectoryName, // application's libraries
			VF_APP_DIR . $libDirectoryName . $library . '/', // application's libraries + samename folder
			VF_SYSTEM_DIR . $libDirectoryName, // core's libraries
			VF_SYSTEM_DIR . $libDirectoryName . $library . '/', // core's libraries + samename folder
		];

		// application's libraries / core's libraries
		foreach ($sourceDirectories as $sourceDirectory) {
			if(is_file($sourceDirectory.$fileName.'.php')){
				require_once($sourceDirectory.$fileName.'.php');
				if (class_exists($className)) {
					self::$instances[$className] = new $className();
					return self::$instances[$className];
				}else{
					throw new Exception('Class <b>'.$className.'</b> no found in file <b>'.$libDirectoryName.$fileName.'.php</b>');
				}
			}
		}

		throw new Exception('File <b>'.$fileName.'.php</b> not found in directories: <b><br/>'.implode('<br/>', $sourceDirectories).'</b>');
	}
}