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
	
	public const REGEX_VALID_BASE64_RFC4648 = '/^[-A-Za-z0-9+\/=]|=[^=]|={3,}$/';

	public static function varchar(int $minlenght, int $maxlenght, $allowNewLine = false)
	{
		if ($maxlenght == $minlenght) {
			return '/^' . ($allowNewLine ? '[\S\s]' : '[^\n]') . '{' . $maxlenght . '}$/';
		}
		return '/^' . ($allowNewLine ? '[\S\s]' : '[^\n]') . '{' . $minlenght . ',' . $maxlenght . '}$/';
	}

	public static function onlyCaptureGroup(int $minlenght, int $maxlenght, array $groupElements)
	{
		if ($maxlenght == $minlenght) {
			return '/^[' . implode($groupElements) . ']{' . $maxlenght . '}$/';
		}
		return '/^[' . implode($groupElements) . ']{' . $minlenght . ',' . $maxlenght . '}$/';
	}

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

	public static function test($pattern, $string)
	{
		return preg_match($pattern, $string);
	}
}
