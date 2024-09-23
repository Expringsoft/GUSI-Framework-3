<?php

namespace App\Core\Framework\Classes;

use App\Core\Application\Configuration;
use JsonException;
use App\Core\Server\Session;
use App\Core\Exceptions\LangException;
use App\Core\Server\Logger;
use App\Core\Framework\Abstracts\SingletonInstance;
use App\Core\Server\Router;

/**
 * LanguageManager manages the language data for the application.
 */
class LanguageManager extends SingletonInstance
{
	/**
	 * @var bool $usingDefaultLang Indicates whether the default language is being used.
	 */
	private $usingDefaultLang = false;

	/**
	 * @var string $language The current language being used.
	 */
	private $language;

	/**
	 * @var array $languageData The language data for the current language.
	 */
	private $languageData;

	/**
	 * @var array $defaultLanguageData The language data for the default language.
	 */
	private $defaultLanguageData;

	/**
	 * Constructs a new LanguageManager instance.
	 *
	 * @param string|null $language The language to use. If null, the language will be detected or retrieved from the session.
	 */
	function __construct($language = null)
	{
		if ($language === null) {
			if (Session::isset(Configuration::APP_LANG_SESSION_KEY)) {
				$language = Session::get(Configuration::APP_LANG_SESSION_KEY);
			} else {
				$language = $this->detectLanguage();
			}
		}
		$this->setLanguage($language);
		$this->loadDefaultLanguageData();
	}

	/**
	 * Sets the current language.
	 *
	 * @param string $language The language to set.
	 */
	public function setLanguage($language)
	{
		$this->language = $language;
		Session::set(Configuration::APP_LANG_SESSION_KEY, $this->language);
		$this->loadLanguageData($this->language);
	}

	/**
	 * Loads the language data for the current language.
	 */
	private function loadLanguageData()
	{
		$filePath = "App/Langs/{$this->language}.json";

		if (!file_exists($filePath)) {
			$this->usingDefaultLang = true;
			$this->loadDefaultLanguageData();
			return;
		}

		$json = file_get_contents($filePath);
		$this->languageData = json_decode($json, true);

		if (json_last_error() !== JSON_ERROR_NONE) {
			Logger::LogError(self::class, "Error decoding language file \"{$this->language}\": " . json_last_error_msg() . ". On {$filePath}. Loading default language file.");
			$this->usingDefaultLang = true;
			$this->loadDefaultLanguageData();
		}
	}

	/**
	 * Loads the language data for the default language.
	 *
	 * @throws LangException If the default language file does not exist.
	 */
	private function loadDefaultLanguageData()
	{
		if ($this->defaultLanguageData !== null) {
			return;
		}

		$filePath = "App/Langs/default.json";

		if (!file_exists($filePath)) {
			throw new LangException("Language file \"default\" does not exist.");
		}

		$json = file_get_contents($filePath);
		$this->defaultLanguageData = json_decode($json, true);

		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new JsonException('Error decoding language file: ' . json_last_error_msg());
		}
	}

	/**
	 * Retrieves the language data for the specified key.
	 *
	 * @param string $key The key to retrieve the language data for.
	 * @return mixed|null The language data for the key, or null if the key does not exist.
	 */
	public function get($key)
	{
		if (isset($this->languageData[$key]) && !$this->usingDefaultLang) {
			return $this->languageData[$key];
		} else {
			if (!isset($this->defaultLanguageData)) {
				$this->loadDefaultLanguageData();
			}
			if (isset($this->defaultLanguageData[$key])) {
				if (!$this->usingDefaultLang) {
					Logger::LogWarning(self::class, "The language key '{$key}' does not exist in lang file '{$this->language}.json'. Using default lang file.");
				}
				return $this->defaultLanguageData[$key];
			} else {
				Logger::LogWarning(self::class, "The language key '{$key}' does not exist in default lang file.");
				return null;
			}
		}
	}

	/**
	 * Detects the user's language.
	 *
	 * @return string The detected language.
	 */
	private function detectLanguage()
	{
		return Router::getUserLanguage();
	}
}