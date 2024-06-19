<?php
namespace App\Core\Server;

use App\Core\Server\Logger;

/**
 * The Session class provides methods for managing session data.
 */
class Session{
	/**
	 * Starts the session if it is not already active and regenerates the session ID.
	 */
	public static function start(){
		if (self::getStatus() == PHP_SESSION_ACTIVE) {
			session_start();
			self::regenerate();
		}
	}

	/**
	 * Sets a session variable.
	 *
	 * @param string|array $key The key or array of keys to set.
	 * @param mixed $value The value to set.
	 */
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

	/**
	 * Checks if a session variable is set.
	 *
	 * @param string $key The key to check.
	 * @return bool Returns true if the session variable is set, false otherwise.
	 */
	public static function isset($key): bool{
		return isset($_SESSION[$key]);
	}

	/**
	 * Gets the value of a session variable.
	 *
	 * @param string|array $key The key or array of keys to get.
	 * @return mixed|null The value of the session variable, or null if it does not exist.
	 */
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

	/**
	 * Deletes a session variable.
	 *
	 * @param string|array $key The key to delete.
	 */
	public static function delete($key)
	{
		if (is_array($key)) {
			$session = &$_SESSION;
			foreach ($key as $segment) {
				if (!isset($session[$segment])) {
					return;
				}
				if ($segment === end($key)) {
					unset($session[$segment]);
				} else {
					$session = &$session[$segment];
				}
			}
		} else {
			unset($_SESSION[$key]);
		}
	}

	/**
	 * Destroys the session if it is active.
	 */
	public static function destroy(){
		if (session_status() == PHP_SESSION_ACTIVE){
			session_destroy();
		}
	}

	/**
	 * Checks if a session variable exists.
	 *
	 * @param string $key The key to check.
	 * @return bool Returns true if the session variable exists, false otherwise.
	 */
	public static function exists($key){
		return isset($_SESSION[$key]);
	}

	/**
	 * Clears all session variables.
	 */
	public static function clear(){
		$_SESSION = [];
	}

	/**
	 * Regenerates the session ID.
	 */
	public static function regenerate(){
		session_regenerate_id();
	}

	/**
	 * Sets a flash message.
	 *
	 * @param string $key The key to set.
	 * @param mixed $value The value to set.
	 */
	public static function setFlash($key, $value)
	{
		self::set($key, $value);
		self::set('flash_keys', array_merge(self::get('flash_keys', []), [$key]));
	}

	/**
	 * Gets a flash message.
	 *
	 * @param string $key The key to get.
	 * @return mixed|null The value of the flash message, or null if it does not exist.
	 */
	public static function getFlash($key)
	{
		$value = self::get($key);
		if (in_array($key, self::get('flash_keys', []))) {
			self::delete($key);
			self::set('flash_keys', array_diff(self::get('flash_keys', []), [$key]));
		}
		return $value;
	}

	/**
	 * Destroys all flash messages.
	 */
	public static function destroyFlash()
	{
		foreach (self::get('flash_keys', []) as $key) {
			self::delete($key);
		}
		self::delete('flash_keys');
	}

	/**
	 * Destroys all flash messages and clears all session variables.
	 */
	public static function destroyAll()
	{
		self::destroyFlash();
		self::clear();
	}

	/**
	 * Gets the session ID.
	 *
	 * @return string The session ID.
	 */
	public static function getId(): string{
		return session_id();
	}

	/**
	 * Sets the session ID.
	 *
	 * @param string $id The session ID to set.
	 */
	public static function setId($id){
		session_id($id);
	}

	/**
	 * Gets the session name.
	 *
	 * @return string The session name.
	 */
	public static function getName(): string{
		return session_name();
	}

	/**
	 * Sets the session name.
	 *
	 * @param string $name The session name to set.
	 */
	public static function setName($name){
		session_name($name);
	}

	/**
	 * Gets the session save path.
	 *
	 * @return string The session save path.
	 */
	public static function getSavePath(): string{
		return session_save_path();
	}

	/**
	 * Sets the session save path.
	 *
	 * @param string $path The session save path to set.
	 */
	public static function setSavePath($path){
		session_save_path($path);
	}

	/**
	 * Gets the session cookie parameters.
	 *
	 * @return array The session cookie parameters.
	 */
	public static function getCookieParams(): array{
		return session_get_cookie_params();
	}

	/**
	 * Sets the session cookie parameters.
	 *
	 * @param array $params The session cookie parameters to set.
	 */
	public static function setCookieParams($params){
		session_set_cookie_params($params);
	}

	/**
	 * Gets the session status.
	 *
	 * @return int The session status.
	 */
	public static function getStatus(): int{
		return session_status();
	}
}