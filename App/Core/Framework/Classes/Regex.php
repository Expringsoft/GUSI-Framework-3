<?php

namespace App\Core\Framework\Classes;

class Regex
{
	public const REGEX_ONLY_INT = '/^-?\d+$/';
	public const REGEX_ONLY_FLOAT = '/^-?\d+(\.\d+)?$/';
	public const REGEX_ONLY_FLOAT_OR_NULL = '/^-?\d*(\.\d+)?$/';
	public const REGEX_DATE_YYYY_MM_DD = '/^\d{4}-\d{2}-\d{2}$/';
	public const REGEX_TIMESTAMP_YYYY_MM_DD_HH_MM_SS = '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/';
	public const REGEX_TIME_HH_MM = '/^\d{2}:\d{2}$/';
	public const REGEX_ONLY_INT_OR_NULL = '/^-?\d*$/';
	public const REGEX_EXCLUDE_NEWLINE = '/^[^\n]*$/';
	public const REGEX_EXCLUDE_NEWLINE_AND_NULL = '/^[^\n]+$/';
	public const REGEX_BOOL_NUMERIC = '/^[0-1]{1}$/';
	public const REGEX_BOOL_VALUE = '/^(true|false)$/';
	public const REGEX_ONLY_ALPHA = '/^[a-zA-Z]+$/';
	public const REGEX_ONLY_ALPHA_AND_NULL = '/^[a-zA-Z]*$/';
	public const REGEX_ONLY_ALPHA_NUMERIC = '/^[a-zA-Z0-9]+$/';
	public const REGEX_ONLY_ALPHA_NUMERIC_AND_NULL = '/^[a-zA-Z0-9]*$/';
	public const REGEX_ONLY_ALPHA_NUMERIC_AND_NULL_WITH_SPACES = '/^[a-zA-Z0-9 ]*$/';
	public const REGEX_ONLY_ALPHA_NUMERIC_WITH_SPACES = '/^[a-zA-Z0-9 ]+$/';
	public const REGEX_ONLY_ALPHA_NUMERIC_WITH_SPACES_AND_NULL = '/^[a-zA-Z0-9 ]*$/';
	public const REGEX_SECURE_PASSWORD = '/^(?=.*[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>]).{8,}$/';
	public const REGEX_VALID_BASE64_RFC4648 = '/^[-A-Za-z0-9+\/=]|=[^=]|={3,}$/';

	/**
	 * Returns a regex pattern for a varchar field.
	 * 
	 * @param int $minlenght The minimum lenght of the string.
	 * @param int $maxlenght The maximum lenght of the string.
	 * @param bool $allowNewLine If the string can contain new lines.
	 * @return string The regex pattern.
	 */
	public static function varchar(int $minlenght, int $maxlenght, $allowNewLine = false)
	{
		if ($maxlenght == $minlenght) {
			return '/^' . ($allowNewLine ? '[\S\s]' : '[^\n]') . '{' . $maxlenght . '}$/';
		}
		return '/^' . ($allowNewLine ? '[\S\s]' : '[^\n]') . '{' . $minlenght . ',' . $maxlenght . '}$/';
	}

	/**
	 * Returns a regex pattern limied to a specific capture group.
	 * 
	 * @param int $minlenght The minimum lenght of the string.
	 * @param int $maxlenght The maximum lenght of the string.
	 * @param array $groupElements The elements to capture (e.g. [a-z], [0-9], etc).
	 */
	public static function onlyCaptureGroup(int $minlenght, int $maxlenght, array $groupElements)
	{
		if ($maxlenght == $minlenght) {
			return '/^[' . implode($groupElements) . ']{' . $maxlenght . '}$/';
		}
		return '/^[' . implode($groupElements) . ']{' . $minlenght . ',' . $maxlenght . '}$/';
	}

	/**
	 * Returns a regex pattern for an integer field.
	 * 
	 * @param int $minlenght The minimum lenght of the string.
	 * @param int $maxlenght The maximum lenght of the string.
	 * @return string The regex pattern.
	 */
	public static function int(int $minlenght, int $maxlenght)
	{
		if ($maxlenght == $minlenght) {
			return '/^[\d]{' . $maxlenght . '}$/';
		}
		return '/^[\d]{' . $minlenght . ',' . $maxlenght . '}$/';
	}

	public static function text()
	{
		return '/^[\S\s]*$/';
	}

	/**
	 * Tests a string against a regex pattern.
	 * 
	 * @param string $pattern The regex pattern.
	 * @param string $string The string to test.
	 * @return int returns 1 if the pattern matches given subject, 0 if it does not, or FALSE if an error occurred.
	 */
	public static function test($pattern, $string)
	{
		return preg_match($pattern, $string);
	}
}
