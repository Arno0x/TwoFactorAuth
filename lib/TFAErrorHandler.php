<?php
/**
 * TwoFactorAuth main configuration file
 *
 * @author Arno0x0x - https://twitter.com/Arno0x0x
 * @license GPLv3 - licence available here: http://www.gnu.org/copyleft/gpl.html
 * @link https://github.com/Arno0x/
 */

//========================================================================
// Suppress fatal errors in the config file
//========================================================================

class TFAErrorHandler
{
	public static function handle_fatal_error() {
		$error = @error_get_last();

		if ($error) {
			switch ($error['type']) {
				case E_ERROR:
				case E_PARSE:
				case E_CORE_ERROR:
				case E_COMPILE_ERROR:
				case E_USER_ERROR:
				case E_RECOVERABLE_ERROR:
				{
					// don't authenticate!!
					http_response_code(401);
					break;
				}
			}
		}
	}

	public static function handle_exception($e) {
		// don't authenticate!!
		http_response_code(401);
	}

	public static function handle_php_error($error_type, $error_string, $file, $line) {

	}
}
