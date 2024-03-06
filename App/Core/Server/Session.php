<?php
namespace App\Core\Server;

use App\Core\Server\Logger;

class Session{
	public static function start(){
		if (session_status() != PHP_SESSION_ACTIVE){
			session_start();
			self::regenerate();
		}
	}

	public static function set($key, $value)
	{
		if (is_array($key)) {
			$session = &$_SESSION;
			foreach ($key as $segment) {
				if (!isset($session[$segment]) || !is_array($session[$segment])) {
					$session[$segment] = [];
				}
				$session = &$session[$segment];
			}
			$session = $value;
		} else {
			$_SESSION[$key] = $value;
		}
	}

	public static function isset($key): bool{
		return isset($_SESSION[$key]);
	}

	public static function get($key)
	{
		if (is_array($key)) {
			$session = $_SESSION;
			foreach ($key as $segment) {
				if (!isset($session[$segment])) {
					Logger::LogWarning(self::class, "The session key '{$segment}' does not exist.");
					return null;
				}
				$session = $session[$segment];
			}
			return $session;
		} else {
			if (isset($_SESSION[$key])) {
				return $_SESSION[$key];
			} else {
				Logger::LogWarning(self::class, "The session key '{$key}' does not exist.");
				return null;
			}
		}
	}

	public static function delete($key){
		unset($_SESSION[$key]);
	}

	public static function destroy(){
		if (session_status() == PHP_SESSION_ACTIVE){
			session_destroy();
		}
	}

	public static function exists($key){
		return isset($_SESSION[$key]);
	}

	public static function clear(){
		$_SESSION = [];
	}

	public static function regenerate(){
		session_regenerate_id();
	}

	public static function setFlash($key, $value){
		self::set($key, $value);
	}
}