<?php
namespace App\Core\Framework\Classes;

use JsonException;
use App\Core\Server\Session;
use App\Core\Exceptions\LangException;
use App\Core\Server\Logger;
use App\Core\Framework\Abstracts\SingletonInstance;
use App\Core\Server\Router;

class LanguageManager extends SingletonInstance
{
	private $usingDefaultLang = false;
	private $language;
	private $languageData;
	private $defaultLanguageData;

	function __construct($language = null)
	{
		if ($language === null) {
			if (Session::isset('language')) {
				$language = Session::get('language');
			} else {
				$language = $this->detectLanguage();
			}
		}
		$this->setLanguage($language);
		$this->loadDefaultLanguageData();
	}

	public function setLanguage($language)
	{
		$this->language = $language;
		Session::set('language', $this->language);
		$this->loadLanguageData($this->language);
	}

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
			Logger::LogError(self::class, "Error decoding language file \"{$this->language}\": " . json_last_error_msg().". On {$filePath}. Loading default language file.");
			$this->usingDefaultLang = true;
			$this->loadDefaultLanguageData();
		}
	}

	private function loadDefaultLanguageData()
	{
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

	public function get($key)
	{
		if (isset($this->languageData[$key]) && !$this->usingDefaultLang) {
			return $this->languageData[$key];
		} elseif (isset($this->defaultLanguageData[$key])) {
			if(!$this->usingDefaultLang){
				Logger::LogWarning(self::class, "The language key '{$key}' does not exist in lang file '{$this->language}.json'. Using default lang file.");
			}
			return $this->defaultLanguageData[$key];
		} else {
			Logger::LogWarning(self::class, "The language key '{$key}' does not exist in default lang file.");
			return null;
		}
	}

	private function detectLanguage()
	{
		return Router::getUserLanguage();
	}
}