<?php
namespace App\Core\Server;

use App\Core\Application\Configuration;
use App\Core\Framework\Enumerables\DataUnits;
use App\Core\Server\FileManager;
use App\Core\Server\Logger;

/**
 * The CacheManager class responsible for managing the cache.
 */
class CacheManager {

	/**
	 * Gets if the cache is valid for the specified key by checking the expiration time and cache file existence.
	 * 
	 * @param string $key The cache key.
	 * @return bool True if the cache is valid, false otherwise.
	 */
	public static function isCacheValid($key): bool
	{
		$cacheFile = Configuration::CACHE_FOLDER . md5($key) . Configuration::CACHE_FILE_EXTENSION;
		clearstatcache($cacheFile);
		if (file_exists($cacheFile)) {
			try {
				$data = unserialize(file_get_contents($cacheFile));
				if (isset($data['expires']) && is_int($data['expires'])) {
					return $data['expires'] > time();
				} else {
					return false;
				}
			} catch (\Throwable $th) {
				Logger::LogError("CacheManager", "Failed to read cache file: " . $th->getMessage());
				return false;
			}
		}
		return false;
	}

	/**
	 * Gets the cache data for the specified key.
	 * 
	 * @param string $key The cache key.
	 */
	public static function getCache($key) {
		if (self::isCacheValid($key)) {
			$cacheFile = Configuration::CACHE_FOLDER . md5($key) . Configuration::CACHE_FILE_EXTENSION;
			$data = unserialize(file_get_contents($cacheFile));
			return $data['data'];
		}
		return false;
	}

	/**
	 * Sets the cache data for the specified key.
	 * 
	 * @param string $key The cache key.
	 * @param mixed $data The cache data.
	 * @param int $expires The expiration time in seconds (default is 3600 seconds).
	 * @return int|false The number of bytes written to the file, or false if failed (storage limit exceeded or max cache size exceeded).
	 */
	public static function writeCache($key, $data, $expires = 3600): int|false
	{
		if (!FileManager::isStorageUsageWithinLimits()) {
			return false;
		}
		$cacheFile = Configuration::CACHE_FOLDER . md5($key) . Configuration::CACHE_FILE_EXTENSION;
		$cacheData = ['data' => $data, 'expires' => time() + $expires];
		$serializedData = serialize($cacheData);
		FileManager::createFile($cacheFile, $serializedData, true);
		clearstatcache($cacheFile);
		if (FileManager::getFileSize($cacheFile, DataUnits::MEGABYTES) > Configuration::MAX_CACHE_SIZE_MB) {
			unlink($cacheFile);
			return false;
		}
		return FileManager::getFileSize($cacheFile, DataUnits::BYTES);
	}

	/**
	 * Deletes the cache for the specified key.
	 * 
	 * @param string $key The cache key.
	 * @return bool True if the cache was deleted, false otherwise.
	 */
	public static function deleteCache($key): bool
	{
		$cacheFile = Configuration::CACHE_FOLDER . md5($key) . Configuration::CACHE_FILE_EXTENSION;
		if (file_exists($cacheFile)) {
			return unlink($cacheFile);
		}
		return false;
	}

	/**
	 * Deletes all cache files.
	 * 
	 * @return void
	 */
	public static function clearExpiredCache(): void
	{
		$files = glob(Configuration::CACHE_FOLDER . '*' . Configuration::CACHE_FILE_EXTENSION);
		foreach ($files as $file) {
			$data = unserialize(file_get_contents($file));
			if ($data['expires'] < time()) {
				unlink($file);
			}
		}
	}

	/**
	 * Deletes all cache files.
	 * 
	 * @return void
	 */
	public static function clearAllCache(): void
	{
		$files = glob(Configuration::CACHE_FOLDER . '*' . Configuration::CACHE_FILE_EXTENSION);
		foreach ($files as $file) {
			unlink($file);
		}
	}
}