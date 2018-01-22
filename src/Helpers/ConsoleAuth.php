<?php
/**
 * Holds the ConsoleAuth Helper.
 * @package Miiverse
 */

namespace Miiverse\Helpers;

use stdClass;

/**
 * Handles the data headers sent by Miiverse.
 * @package Miiverse
 * @author Repflez
 */
class ConsoleAuth {
	/**
	 * The ParamPack of the current console.
	 * @var array
	 */
	public static $paramPack = [];

	/**
	 * The friend PID of the current console.
	 * @var object
	 */
	public static $consoleId;

	/**
	 * Checks the Console Auth
	 */
	public static function check() {
		if (!isset($_SERVER['HTTP_X_NINTENDO_PARAMPACK']) || !isset($_SERVER['HTTP_X_NINTENDO_SERVICETOKEN'])) {
			echo config('general.name'), ' is only for 3DS consoles at the moment.';
			exit;
		}

		$storage = [];

		// Unpack the ParamPack from the headers sent by the console
		// https://github.com/foxverse/3ds/blob/5e1797cdbaa33103754c4b63e87b4eded38606bf/web/titlesShow.php#L37-L40
		$data = explode('\\', base64_decode($_SERVER['HTTP_X_NINTENDO_PARAMPACK']));
		array_shift($data);
		array_pop($data);

		$paramCount = count($data);

		for ($i = 0; $i < $paramCount; $i += 2) {
			$storage[$data[$i]] = $data[$i + 1];
		}

		// Set title id and transferable id to hex, just in case we need it
		$storage['title_id'] = base_convert($storage['title_id'], 10, 16);
		$storage['transferable_id'] = base_convert($storage['transferable_id'], 10, 16);
		$serviceToken = bin2hex(base64_decode($_SERVER['HTTP_X_NINTENDO_SERVICETOKEN']));

		// Store the values for later use
		self::$paramPack = $storage;
		self::$consoleId = new stdClass;

		self::$consoleId->short = substr($serviceToken, 0, 16);
		self::$consoleId->long = substr($serviceToken, 0, 64);
	}
}