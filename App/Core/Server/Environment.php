<?php

namespace App\Core\Server;

use App\Core\Framework\Abstracts\SingletonInstance;

class Environment extends SingletonInstance
{
	private bool $VariablesLoaded = false;
	private array $Variables = [];

	public function __construct()
	{
		$this->loadEnvironmentVariables();
	}

	public function loadEnvironmentVariables()
	{
		if ($this->VariablesLoaded) {
			return;
		}
		$lockFile = sys_get_temp_dir() . '/env.lock';
		$fp = fopen($lockFile, 'c');

		if (flock($fp, LOCK_EX)) {
			if (file_exists('.env')) {
				$envData = file_get_contents('.env');
				$envLines = explode("\n", $envData);
				foreach ($envLines as $line) {
					$line = trim($line);
					if (empty($line) || strpos($line, '#') === 0) {
						continue;
					}
					$parts = explode('=', $line, 2);
					if (count($parts) === 2) {
						$key = trim($parts[0]);
						$value = trim($parts[1]);
						putenv($key . '=' . $value);
						$this->Variables[$key] = $value;
					}
				}
			}
			flock($fp, LOCK_UN);
		}
		fclose($fp);
		$this->VariablesLoaded = true;
	}

	public function getEnvironmentVariable($key)
	{
		if (isset($this->Variables[$key])) {
			return $this->Variables[$key];
		} else {
			Logger::LogWarning("[SERVER][ENVIRONMENT]", "Variable \"" . $key . "\" not found. null returned.");
			return null;
		}
	}
}