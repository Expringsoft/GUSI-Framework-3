<?php

namespace App\Core\Server;

use App\Core\Server\Logger;
use App\Core\Application\Configuration;
use App\Core\Exceptions\SystemIOException;
use App\Core\Exceptions\StorageException;
use App\Core\Framework\Enumerables\DataUnits;
use COM;

class FileManager
{
	// Constants

	// Disk space usage cap in GB
	public const USAGE_CAP_GB = 20;
	// Minimum disk space in GB
	public const MINIMUM_DISK_SPACE_GB = 1;
	// Root files folder
	public const ROOT_FILES_FOLDER = "Files/";

	// Methods

	/** Get file size in specified data units
	*	@param string $file
	*	@param DataUnits $returnType
	*	@return float
	*/
	public static function getFileSize($file, $returnType)
	{
		if (!file_exists($file)) {
			if (Configuration::LOG_ERRORS) {
				Logger::LogWarning("FileManager", "File: '{$file}' not found.");
			}
			return false;
		}
		$size = filesize($file);
		if ($size <= 0) {
			if (!(strtoupper(substr(PHP_OS, 0, 3)) == 'WIN')) {
				$size = trim(`stat -c%s $file`);
			} else {
				$fsobj = new COM("Scripting.FileSystemObject");
				$f = $fsobj->GetFile($file);
				$size = $f->Size;
			}
		}
		return self::convertBytesTo($size, $returnType);
	}

	/** Get folder size in specified data units
	 * @param string $folder
	 * @param DataUnits $returnType
	 * @return float
	 * @throws UnexpectedValueException if the folder does not exist
	 */ 
    public static function getFolderSize($folder, $returnType)
    {
        $totalSize = 0;
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($folder, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            $totalSize += $file->getSize();
        }

        return self::convertBytesTo($totalSize, $returnType);
    }
	

	/** Get total disk space in specified data units
	 * @param DataUnits $returnType
	 * @return float
	 */
	public static function getDiskTotalSpace(DataUnits $returnType)
	{
		return self::convertBytesTo(disk_total_space("."), $returnType);
	}

	/** Get available disk space in specified data units
	 * @param DataUnits $returnType
	 * @return float
	 */
	public static function getDiskAvailableSpace(DataUnits $returnType)
	{
		return self::convertBytesTo(disk_free_space("."), $returnType);
	}

	/** Get used disk space in specified data units
	 * @param DataUnits $returnType
	 * @return float
	 */
	public static function getDiskUsedSpace(DataUnits $returnType)
	{
		return self::convertBytesTo(self::getDiskTotalSpace(DataUnits::BYTES) - self::getDiskAvailableSpace(DataUnits::BYTES), $returnType);
	}

	/** Get application used space in specified data units
	 * @param DataUnits $returnType
	 * @return float
	 */
	public static function getAppUsedSpace(DataUnits $returnType)
	{
		return self::convertBytesTo(self::getFolderSize(self::ROOT_FILES_FOLDER, DataUnits::BYTES), $returnType);
	}

	/** Convert bytes to specified data units
	 * @param float $bytes
	 * @param DataUnits $returnType
	 * @return float
	 */
	public static function convertBytesTo($bytes, DataUnits $returnType)
	{
		switch ($returnType) {
			case DataUnits::BYTES:
				return $bytes;
				break;
			case DataUnits::KILOBYTES:
				return $bytes / 1024;
				break;
			case DataUnits::MEGABYTES:
				return $bytes / 1048576;
				break;
			case DataUnits::GIGABYTES:
				return $bytes / 1073741824;
				break;
			case DataUnits::TERABYTES:
				return $bytes / 1099511627776;
				break;
			case DataUnits::PETABYTES:
				return $bytes / 1125899906842624;
				break;
			case DataUnits::EXABYTES:
				return $bytes / 1152921504606846976;
				break;
			case DataUnits::ZETTABYTES:
				return $bytes / 1180591620717411303424;
				break;
			case DataUnits::YOTTABYTES:
				return $bytes / 1208925819614629174706176;
				break;
			default:
				return $bytes;
				break;
		}
	}

	/** Check disk space
	 * @throws StorageException if not enough disk space available
	 */
	public static function checkDiskSpace()
	{
		$availableSpace = self::getDiskAvailableSpace(DataUnits::BYTES);
		if (self::convertBytesTo($availableSpace, DataUnits::GIGABYTES) < self::MINIMUM_DISK_SPACE_GB) {
			throw new StorageException("Not enough disk space available.");
		}
	}

	/** Check application disk usage
	 * @throws StorageException if disk usage exceeded
	 * @throws SystemIOException if failed to get folder size
	 * @throws StorageException if disk has not enough space
	 */
	public static function checkAppDiskUsage()
	{
		self::checkDiskSpace();
		$usedSpace = self::getFolderSize(self::ROOT_FILES_FOLDER, DataUnits::BYTES);
		if (self::convertBytesTo($usedSpace, DataUnits::GIGABYTES) > self::USAGE_CAP_GB) {
			throw new StorageException("Application disk usage exceeded.");
		}
	}

	/** Create folder
	 * @param string $folderName
	 * @throws SystemIOException if failed to create folder
	 */
	public static function createFolder($folderName)
	{
		if (!file_exists($folderName)) {
			if (!mkdir($folderName, 0777, true)) {
				throw new SystemIOException("Failed to create folder: '{$folderName}'");
			}
		}
	}

	/** Delete folder
	 * @param string $folderName
	 * @throws SystemIOException if failed to delete folder
	 */
	public static function deleteFolder($folderName)
	{
		if (file_exists($folderName)) {
			if (!rmdir($folderName)) {
				throw new SystemIOException("Failed to delete folder: '{$folderName}'");
			}
		}
	}

	/** Move folder
	 * @param string $source
	 * @param string $destination
	 * @param bool $ignoreUsageCap false
	 * @throws SystemIOException if failed to move folder
	 * @throws StorageException if disk usage exceeded
	 */
	public static function moveFolder($source, $destination, $ignoreUsageCap = false)
	{
		if (!$ignoreUsageCap) {
			self::checkAppDiskUsage();
		}
		if (!rename($source, $destination)) {
			throw new SystemIOException("Failed to move folder: '{$source}' to '{$destination}'");
		}
	}

	/** Copy folder
	 * @param string $source
	 * @param string $destination
	 * @param bool $ignoreUsageCap false
	 * @throws SystemIOException if failed to copy folder
	 * @throws StorageException if disk usage exceeded
	 */
	public static function copyFolder($source, $destination, $ignoreUsageCap = false)
	{
		if (!$ignoreUsageCap) {
			self::checkAppDiskUsage();
		}
		if (!copy($source, $destination)) {
			throw new SystemIOException("Failed to copy folder: '{$source}' to '{$destination}'");
		}
	}

	/**
	 * Deletes a file from the file system.
	 *
	 * @param string $filePath The path of the file to be deleted.
	 * @throws SystemIOException if the file could not be deleted.
	 */
	public static function deleteFile($fileName)
	{
		if (file_exists($fileName)) {
			if (!unlink($fileName)) {
				throw new SystemIOException("Failed to delete file: '{$fileName}'");
			}
		}
	}

	/** Move file
	 * @param string $source
	 * @param string $destination
	 * @param bool $ignoreUsageCap false
	 * @throws SystemIOException if failed to move file
	 * @throws StorageException if disk usage exceeded
	 */
	public static function moveFile($source, $destination, $ignoreUsageCap = false)
	{
		if (!$ignoreUsageCap) {
			self::checkAppDiskUsage();
		}
		if (!rename($source, $destination)) {
			throw new SystemIOException("Failed to move file: '{$source}' to '{$destination}'");
		}
	}

	/** Copy file
	 * @param string $source
	 * @param string $destination
	 * @param bool $ignoreUsageCap false
	 * @throws SystemIOException if failed to copy file
	 * @throws StorageException if disk usage exceeded
	 */
	public static function copyFile($source, $destination, $ignoreUsageCap = false)
	{
		if (!$ignoreUsageCap) {
			self::checkAppDiskUsage();
		}
		if (!copy($source, $destination)) {
			throw new SystemIOException("Failed to copy file: '{$source}' to '{$destination}'");
		}
	}

	/** Save file
	 * @param string $fileName
	 * @param string $content
	 * @param bool $overwrite false
	 * @throws SystemIOException if failed to save file
	 * @throws StorageException if disk usage exceeded
	 */
	public static function saveFile($fileName, $content, $overwrite = false, $ignoreUsageCap = false)
	{
		if (!$ignoreUsageCap) {
			self::checkAppDiskUsage();
		}
		if (file_exists($fileName) && !$overwrite) {
			throw new SystemIOException("File: '{$fileName}' already exists.");
		}
		if (!file_put_contents($fileName, $content)) {
			throw new SystemIOException("Failed to save file: '{$fileName}'");
		}
	}

	/** Append to file
	 * @param string $fileName
	 * @param string $content
	 * @param bool $ignoreUsageCap false
	 * @throws SystemIOException if failed to append to file
	 */
	public static function appendToFile($fileName, $content, $ignoreUsageCap = false)
	{
		if (!$ignoreUsageCap) {
			self::checkAppDiskUsage();
		}
		if (!file_put_contents($fileName, $content, FILE_APPEND)) {
			throw new SystemIOException("Failed to append to file: '{$fileName}'");
		}
	}
}
