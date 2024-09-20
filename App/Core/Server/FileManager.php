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
	#region Error codes
	public const ERR_APP_STORAGE_USAGE_EXCEEDED = 5001;
	public const ERR_FILE_ALREADY_EXISTS = 5002;
	public const ERR_FILE_EXEEDED_SIZE_LIMIT = 5003;
	public const ERR_FILE_EXTENSION_INVALID = 5004;
	public const ERR_FILE_INVALID = 5005;
	public const ERR_FILE_MIME_TYPE_INVALID = 5006;
	public const ERR_FILE_NAME_INVALID = 5007;
	public const ERR_FILE_NOT_FOUND = 5008;
	public const ERR_FILE_NOT_UPLOADED = 5009;
	public const ERR_FILE_NOT_DELETED = 5010;
	public const ERR_FILE_NOT_RENAMED = 5011;
	public const ERR_FILE_NOT_MOVED = 5012;
	public const ERR_FILE_NOT_COPIED = 5013;
	public const ERR_FILE_NOT_WRITTEN = 5014;
	public const ERR_FILE_NOT_READ = 5015;
	public const ERR_FILE_NOT_OPENED = 5016;
	public const ERR_FILE_NOT_CLOSED = 5017;
	public const ERR_FILE_NOT_LOCKED = 5018;
	public const ERR_FILE_NOT_UNLOCKED = 5019;
	public const ERR_FILE_NOT_CREATED = 5020;
	public const ERR_FILE_NOT_APPENDED = 5021;
	public const ERR_FILE_NOT_PREPENDED = 5022;
	public const ERR_FILE_NOT_PERMISSIONS_CHANGED = 5023;
	public const ERR_FILE_NOT_OWNER_CHANGED = 5024;
	public const ERR_FILE_NOT_GROUP_CHANGED = 5025;
	public const ERR_FILE_NOT_MODIFIED = 5026;
	public const ERR_FILE_NOT_TOUCHED = 5027;
	public const ERR_FILE_NOT_EXECUTED = 5028;
	public const ERR_FOLDER_NOT_CREATED = 5040;
	public const ERR_FOLDER_NOT_DELETED = 5041;
	public const ERR_FOLDER_NOT_MOVED = 5042;
	public const ERR_FOLDER_NOT_COPIED = 5043;
	public const ERR_FOLDER_NOT_CLEARED = 5044;
	public const ERR_FOLDER_NOT_RENAMED = 5045;
	public const ERR_FOLDER_NOT_TOUCHED = 5046;
	public const ERR_FOLDER_PERMISSIONS_NOT_CHANGED = 5047;
	public const ERR_NO_MINIMUM_STORAGE_SPACE = 5080;
	public const ERR_STORAGE_USAGE_OUT_OF_BOUNDS = 5081;
	#endregion

	/** 
	 * Get file size in specified data units
	 * 
	 *	@param string $file
	 *	@param DataUnits $returnType
	 *	@return float|false The file size in the specified data units or false if the file does not exist
	 */
	public static function getFileSize($file, DataUnits $returnType): float|false {
		if (!file_exists($file)) {
			Logger::LogWarning("FileManager", "File: '{$file}' not found.");
			return false;
		}
		$size = filesize($file);
		if ($size <= 0) {
			$handle = fopen($file, "rb");
			if ($handle === false) {
				Logger::LogWarning("FileManager", "Unable to open file: '{$file}'");
				return false;
			}
			$size = 0;
			while (!feof($handle)) {
				$size += strlen(fread($handle, 8192));
			}
			fclose($handle);
		}
		return self::convertBytesTo($size, $returnType);
	}

	/** 
	 * Get folder size in specified data units
	 * 
	 * @param string $folder
	 * @param DataUnits $returnType
	 * @return float
	 * @throws UnexpectedValueException if the folder does not exist
	 */ 
    public static function getFolderSize($folder, DataUnits $returnType): float {
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
	

	/** 
	 * Get total disk space in specified data units
	 * 
	 * @param DataUnits $returnType
	 * @return float
	 */
	public static function getDiskTotalSpace(DataUnits $returnType): float {
		return self::convertBytesTo(disk_total_space("."), $returnType);
	}

	/** 
	 * Get available disk space in specified data units
	 * 
	 * @param DataUnits $returnType
	 * @return float
	 */
	public static function getDiskAvailableSpace(DataUnits $returnType): float {
		return self::convertBytesTo(disk_free_space("."), $returnType);
	}

	/** 
	 * Get used disk space in specified data units
	 * 
	 * @param DataUnits $returnType
	 * @return float
	 */
	public static function getDiskUsedSpace(DataUnits $returnType): float {
		return self::convertBytesTo(self::getDiskTotalSpace(DataUnits::BYTES) - self::getDiskAvailableSpace(DataUnits::BYTES), $returnType);
	}

	/** 
	 * Get application used space in specified data units
	 * 
	 * @param DataUnits $returnType
	 * @return float
	 */
	public static function getAppUsedSpace(DataUnits $returnType): float {
		return self::convertBytesTo(self::getFolderSize(".", DataUnits::BYTES), $returnType);
	}

	/** 
	 * Convert bytes to specified data units
	 * 
	 * @param float $bytes
	 * @param DataUnits $returnType
	 * @return float
	 */
	public static function convertBytesTo($bytes, DataUnits $returnType): float {
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

	/** 
	 * Checks if the disk has the minimum required space
	 * 
	 * @return bool true if the disk has the minimum required space, false otherwise
	 */
	public static function isMinimumDiskSpaceAvailable(): bool {
		return self::getDiskAvailableSpace(DataUnits::GIGABYTES) > Configuration::MINIMUM_DISK_SPACE_GB;
	}

	/** 
	 * Checks if the disk has the minimum required space
	 * 
	 * @param float $size The size to check
	 * @param DataUnits $dataUnits Data units for the size
	 * @return bool true if the disk has the minimum required space, false otherwise
	 */
	public static function hasAvailableDiskSpace(float $size, DataUnits $dataUnits): bool {
		return self::getDiskAvailableSpace($dataUnits) > self::convertBytesTo($size, $dataUnits);
	}

	/** 
	 * Checks if the application disk usage is within the limit
	 * 
	 * @return bool true if the application disk usage is within the limit, false otherwise
	 */
	public static function isAppUsedSpaceWithinLimits(): bool {
		return self::getAppUsedSpace(DataUnits::GIGABYTES) <= Configuration::APP_STORAGE_USAGE_CAP_GB;
	}

	/** 
	 * Checks if the disk has the minimum required space and if the application disk usage is within the limit
	 */
	public static function isStorageUsageWithinLimits(): bool {
		return self::isMinimumDiskSpaceAvailable() && self::isAppUsedSpaceWithinLimits();
	}

	/** 
	 * Checks if the application has the minimum required space
	 * 
	 * @param float $size The size to check
	 * @param DataUnits $dataUnits The data units
	 * @return bool true if the application has the minimum required space, false otherwise
	 */
	public static function hasAvailableAppSpace(float $size, DataUnits $dataUnits): bool {
		return self::getAppUsedSpace($dataUnits) + self::convertBytesTo($size, $dataUnits) <= Configuration::APP_STORAGE_USAGE_CAP_GB;
	}

	/** 
	 * Create folder
	 * 
	 * @param string $folderName The folder to create
	 * @param int $permissions 0777 by default
	 * @param bool $recursive true by default
	 * @throws SystemIOException if failed to create folder
	 */
	public static function createFolder($folderName, $permissions = 0777, $recursive = true): void {
		if (!file_exists($folderName)) {
			if (!mkdir($folderName, $permissions, $recursive)) {
				throw new SystemIOException("Failed to create folder: '{$folderName}'", self::ERR_FOLDER_NOT_CREATED);
			}
		}
	}

	/** 
	 * Delete folder
	 * 
	 * @param string $folderName The folder to delete
	 * @return void
	 * @throws SystemIOException if failed to delete folder
	 */
	public static function deleteFolder($folderName): void {
		if (file_exists($folderName)) {
			if (!rmdir($folderName)) {
				throw new SystemIOException("Failed to delete folder: '{$folderName}'", self::ERR_FOLDER_NOT_DELETED);
			}
		}
	}

	/** 
	 * Delete folder tree
	 * 
	 * @param string $dir The directory to delete
	 * @return bool true if the directory was deleted, false otherwise
	 */
	public static function deleteTree($dir): bool {
		$files = array_diff(scandir($dir), array('.','..'));
		foreach ($files as $file) {
			(is_dir("$dir/$file")) ? self::deleteTree("$dir/$file") : unlink("$dir/$file");
		}
		return rmdir($dir);
	}

	/** 
	 * Clear folder contents
	 * 
	 * @param string $dir The directory to clear
	 * @return void
	 */
	public static function clearFolder($dir): void {
		$files = glob($dir . '/*');
		foreach ($files as $file) {
			is_dir($file) ? self::deleteTree($file) : unlink($file);
		}
	}

	/** 
	 * Move folder
	 * 
	 * @param string $source The source folder
	 * @param string $destination The destination folder
	 * @param bool $ignoreUsageCap If true, the app storage usage cap will be ignored, false by default
	 * @throws SystemIOException if failed to move folder
	 * @throws StorageException if the disk does not have the minimum required space or the app disk usage exceeded (if ignoreUsageCap is false)
	 */
	public static function moveFolder($source, $destination, $ignoreUsageCap = false): void {
		if (!self::isMinimumDiskSpaceAvailable()) {
			throw new StorageException("Could not perform this action: Minimum disk space not available", self::ERR_NO_MINIMUM_STORAGE_SPACE);
		}
		if (!$ignoreUsageCap && !self::isAppUsedSpaceWithinLimits()) {
			throw new StorageException("Could not move folder: App disk usage exceeded", self::ERR_APP_STORAGE_USAGE_EXCEEDED);
		}
		if (!rename($source, $destination)) {
			throw new SystemIOException("Failed to move folder: '{$source}' to '{$destination}'", self::ERR_FOLDER_NOT_MOVED);
		}
	}

	/** 
	 * Copy folder
	 * 
	 * @param string $source The source folder
	 * @param string $destination The destination folder
	 * @param bool $ignoreUsageCap If true, the app storage usage cap will be ignored, false by default 
	 * @throws SystemIOException if failed to copy folder
	 * @throws StorageException if the disk does not have the minimum required space or the app disk usage exceeded (if ignoreUsageCap is false)
	 */
	public static function copyFolder($source, $destination, $ignoreUsageCap = false): void {
		if (!self::isMinimumDiskSpaceAvailable()) {
			throw new StorageException("Could not perform this action: Minimum disk space not available", self::ERR_NO_MINIMUM_STORAGE_SPACE);
		}
		if (!$ignoreUsageCap && !self::isAppUsedSpaceWithinLimits()) {
			throw new StorageException("Could not copy folder: App disk usage exceeded", self::ERR_APP_STORAGE_USAGE_EXCEEDED);
		}
		if (!copy($source, $destination)) {
			throw new SystemIOException("Failed to copy folder: '{$source}' to '{$destination}'", self::ERR_FOLDER_NOT_COPIED);
		}
	}

	/**
	 * Deletes a file from the file system.
	 *
	 * @param string $filePath The path of the file to be deleted.
	 * @throws SystemIOException if the file could not be deleted.
	 */
	public static function deleteFile($fileName): void {
		if (file_exists($fileName)) {
			if (!unlink($fileName)) {
				throw new SystemIOException("Failed to delete file: '{$fileName}'", self::ERR_FILE_NOT_DELETED);
			}
		} else {
			throw new SystemIOException("Failed to delete file: '{$fileName}'", self::ERR_FILE_NOT_FOUND);
		}
	}

	/** 
	 * Move file
	 * 
	 * @param string $source The source file
	 * @param string $destination The destination file
	 * @param bool $ignoreUsageCap If true, the app storage usage cap will be ignored, false by default
	 * @throws SystemIOException if failed to move file
	 * @throws StorageException if the disk does not have the minimum required space or the app disk usage exceeded (if ignoreUsageCap is false)
	 */
	public static function moveFile($source, $destination, $ignoreUsageCap = false): void {
		if (!self::isMinimumDiskSpaceAvailable()) {
			throw new StorageException("Could not perform this action: Minimum disk space not available", self::ERR_NO_MINIMUM_STORAGE_SPACE);
		}
		if (!$ignoreUsageCap && !self::isAppUsedSpaceWithinLimits()) {
			throw new StorageException("Could not move file: App disk usage exceeded", self::ERR_APP_STORAGE_USAGE_EXCEEDED);
		}
		if (!rename($source, $destination)) {
			throw new SystemIOException("Failed to move file: '{$source}' to '{$destination}'", self::ERR_FILE_NOT_MOVED);
		}
	}

	/** 
	 * Copy file
	 * 
	 * @param string $source The source file
	 * @param string $destination The destination file
	 * @param bool $ignoreUsageCap If true, the app storage usage cap will be ignored, false by default
	 * @throws SystemIOException if failed to copy file
	 * @throws StorageException if the disk does not have the minimum required space or the app disk usage exceeded (if ignoreUsageCap is false)
	 */
	public static function copyFile($source, $destination, $ignoreUsageCap = false): void {
		if (!self::isMinimumDiskSpaceAvailable()) {
			throw new StorageException("Could not perform this action: Minimum disk space not available", self::ERR_NO_MINIMUM_STORAGE_SPACE);
		}
		if (!$ignoreUsageCap && !self::isAppUsedSpaceWithinLimits()) {
			throw new StorageException("Could not copy file: App disk usage exceeded", self::ERR_APP_STORAGE_USAGE_EXCEEDED);
		}
		if (!copy($source, $destination)) {
			throw new SystemIOException("Failed to copy file: '{$source}' to '{$destination}'", self::ERR_FILE_NOT_COPIED);
		}
	}

	/** 
	 * Create file
	 * 
	 * @param string $fileName The full path of the file to create
	 * @param string $content File content, empty by default
	 * @param int $permissions Permissions for the file, 0777 by default
	 * @param bool $overwrite If true, the file will be overwritten if it already exists, false by default
	 * @param bool $ignoreUsageCap If true, the app usage cap will be ignored, false by default
	 * @return int|false The number of bytes written to the file or false if failed to create file
	 * @throws SystemIOException if failed to create file
	 * @throws StorageException if the disk does not have the minimum required space or the app disk usage exceeded (if ignoreUsageCap is false)
	 */
	public static function createFile($fileName, $content = '', $overwrite = false, $permissions = 0777, $ignoreUsageCap = false): int|false {
		if (!self::isMinimumDiskSpaceAvailable()) {
			throw new StorageException("Could not perform this action: Minimum disk space not available", self::ERR_NO_MINIMUM_STORAGE_SPACE);
		}
		if (!$ignoreUsageCap && !self::isAppUsedSpaceWithinLimits()) {
			throw new StorageException("Could not create file: App disk usage exceeded", self::ERR_APP_STORAGE_USAGE_EXCEEDED);
		}
		if (file_exists($fileName) && !$overwrite) {
			throw new SystemIOException("File: '{$fileName}' already exists.", self::ERR_FILE_ALREADY_EXISTS);
		}
		$result = file_put_contents($fileName, $content);
		if (!$result || !chmod($fileName, $permissions)) {
			throw new SystemIOException("Failed to create file: '{$fileName}'", self::ERR_FILE_NOT_CREATED);
		}
		return $result;
	}

	/** 
	 * Save file content without modifying the file permissions
	 * 
	 * @param string $fileName The full path of the file to save
	 * @param string $content The content to save
	 * @param bool $overwrite If true, the file will be overwritten if it already exists, false by default
	 * @throws SystemIOException if failed to save file
	 * @throws StorageException if the disk does not have the minimum required space or the app disk usage exceeded (if ignoreUsageCap is false)
	 */
	public static function saveFile($fileName, $content, $overwrite = false, $ignoreUsageCap = false): void {
		if (!self::isMinimumDiskSpaceAvailable()) {
			throw new StorageException("Could not perform this action: Minimum disk space not available", self::ERR_NO_MINIMUM_STORAGE_SPACE);
		}
		if (!$ignoreUsageCap && !self::isAppUsedSpaceWithinLimits()) {
			throw new StorageException("Could not save file: App disk usage exceeded", self::ERR_APP_STORAGE_USAGE_EXCEEDED);
		}
		if (file_exists($fileName) && !$overwrite) {
			throw new SystemIOException("File: '{$fileName}' already exists.", self::ERR_FILE_ALREADY_EXISTS);
		}
		if (!file_put_contents($fileName, $content)) {
			throw new SystemIOException("Failed to save file: '{$fileName}'", self::ERR_FILE_NOT_WRITTEN);
		}
	}

	/** 
	 * Append to file
	 * 
	 * @param string $fileName The full path of the file to append to
	 * @param string $content The content to append
	 * @param bool $ignoreUsageCap If true, the app usage cap will be ignored, false by default
	 * @throws SystemIOException if failed to append to file
	 * @throws StorageException if the disk does not have the minimum required space or the app disk usage exceeded (if ignoreUsageCap is false)
	 */
	public static function appendToFile($fileName, $content, $ignoreUsageCap = false): void {
		if (!self::isMinimumDiskSpaceAvailable()) {
			throw new StorageException("Could not perform this action: Minimum disk space not available", self::ERR_NO_MINIMUM_STORAGE_SPACE);
		}
		if (!$ignoreUsageCap && !self::isAppUsedSpaceWithinLimits()) {
			throw new StorageException("Could not append to file: App disk usage exceeded", self::ERR_APP_STORAGE_USAGE_EXCEEDED);
		}
		if (!file_put_contents($fileName, $content, FILE_APPEND)) {
			throw new SystemIOException("Failed to append to file: '{$fileName}'", self::ERR_FILE_NOT_APPENDED);
		}
	}

	/** 
	 * Prepend to file
	 * 
	 * @param string $fileName The full path of the file to prepend to
	 * @param string $content The content to prepend
	 * @param bool $ignoreUsageCap If true, the app usage cap will be ignored, false by default
	 * @throws SystemIOException if failed to prepend to file
	 * @throws StorageException if the disk does not have the minimum required space or the app disk usage exceeded (if ignoreUsageCap is false)
	 */
	public static function prependToFile($fileName, $content, $ignoreUsageCap = false): void {
		if (!self::isMinimumDiskSpaceAvailable()) {
			throw new StorageException("Could not perform this action: Minimum disk space not available", self::ERR_NO_MINIMUM_STORAGE_SPACE);
		}
		if (!$ignoreUsageCap && !self::isAppUsedSpaceWithinLimits()) {
			throw new StorageException("Could not prepend file: App disk usage exceeded", self::ERR_APP_STORAGE_USAGE_EXCEEDED);
		}
		$existingContent = file_get_contents($fileName);
		if (!file_put_contents($fileName, $content . $existingContent)) {
			throw new SystemIOException("Failed to prepend to file: '{$fileName}'", self::ERR_FILE_NOT_APPENDED);
		}
	}

	/** 
	 * Read file
	 * 
	 * @param string $fileName The full path of the file to read
	 * @return string|false The file content or false if failed on failure
	 * @throws SystemIOException if failed to read file
	 */
	public static function readFile($fileName = null): string|false {
		if (!file_exists($fileName)) {
			throw new SystemIOException("File: '{$fileName}' not found.", self::ERR_FILE_NOT_FOUND);
		}
		return file_get_contents($fileName);
	}

	/** 
	 * Rename file
	 * 
	 * @param string $source The source file
	 * @param string $destination The destination file
	 * @param bool $ignoreUsageCap If true, the app usage cap will be ignored, false by default
	 * @throws SystemIOException if failed to rename file or file already exists
	 * @throws StorageException if the disk does not have the minimum required space or the app disk usage exceeded (if ignoreUsageCap is false)
	 */
	public static function renameFile($source, $destination, $overwrite = false, $ignoreUsageCap = false): void {
		if (!self::isMinimumDiskSpaceAvailable()) {
			throw new StorageException("Could not perform this action: Minimum disk space not available", self::ERR_NO_MINIMUM_STORAGE_SPACE);
		}
		if (!$ignoreUsageCap && !self::isAppUsedSpaceWithinLimits()) {
			throw new StorageException("Could not rename file: App disk usage exceeded", self::ERR_APP_STORAGE_USAGE_EXCEEDED);
		}
		if (file_exists($destination) && !$overwrite) {
			throw new SystemIOException("File: '{$destination}' already exists.", self::ERR_FILE_ALREADY_EXISTS);
		}
		if (!rename($source, $destination)) {
			throw new SystemIOException("Failed to rename file: '{$source}' to '{$destination}'", self::ERR_FILE_NOT_RENAMED);
		}
	}

	/**
	 * Sanitize the filename to avoid security issues.
	 *
	 * @param string $filename The filename to sanitize
	 * @return string The sanitized filename
	 */
	public static function sanitizeFilename(string $filename): string
	{
		// Regular expression to match invalid characters in a filename
		$invalid_chars_regex = '/[^\p{L}\p{N} _\-.,()áéíóúÁÉÍÓÚüÜñÑ]|[\/:*?"<>|]|^( |\.)|^[ \.]+$|^(CON|PRN|AUX|NUL|COM[1-9]|LPT[1-9])$/u';
		// Replace invalid characters with underscores
		$sanitized_filename = preg_replace($invalid_chars_regex, '_', $filename);
		// Remove leading dots and spaces
		$sanitized_filename = ltrim($sanitized_filename, ' .');
		// For Windows, check for reserved names and add an underscore at the end of the filename if the filename is reserved.
		$reserved_names = ['CON', 'PRN', 'AUX', 'NUL', 'COM1', 'COM2', 'COM3', 'COM4', 'COM5', 'COM6', 'COM7', 'COM8', 'COM9', 'LPT1', 'LPT2', 'LPT3', 'LPT4', 'LPT5', 'LPT6', 'LPT7', 'LPT8', 'LPT9'];
		if (in_array(strtoupper($sanitized_filename), $reserved_names)) {
			$sanitized_filename = $sanitized_filename . '_';
		}
		return $sanitized_filename;
	}

	/** 
	 * Get file extension
	 * 
	 * @param string $fileName The file name
	 * @return string The file extension or an empty string if the file has no extension
	 */
	public static function getFileExtension($fileName): string {
		return pathinfo($fileName, PATHINFO_EXTENSION) ?? '';
	}

	/**
	 * Get the real file type of a specified file.
	 *
	 * @param string $fileName The path of the file.
	 * @return string The real file type.
	 * @throws SystemIOException if failed to determine the file type or if the file does not exist.
	 */
	public static function getFileMimeType($fileName): string {
		if (!file_exists($fileName)) {
			throw new SystemIOException("Failed to determine the file mime type. File: '{$fileName}' not found.", self::ERR_FILE_NOT_FOUND);
		}
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$fileType = finfo_file($finfo, $fileName);
		finfo_close($finfo);
		if ($fileType === false) {
			throw new SystemIOException("Failed to determine the file mime type of '{$fileName}'", self::ERR_FILE_INVALID);
		}
		return $fileType;
	}

	/** 
	 * Get file name
	 * 
	 * @param string $fileName The file name with extension
	 * @return string The file name
	 */
	public static function getFileName($fileName): string {
		return pathinfo($fileName, PATHINFO_FILENAME);
	}

	/** 
	 * Get file last modified
	 * 
	 * @param string $fileName The file name
	 * @return int|false Unix timestamp or false if failed to get last modified time
	 */
	public static function getFileLastModified($fileName): int|false {
		return filemtime($fileName);
	}

	/**
	 * Checks if a file name is valid (contains only alphanumeric characters, underscores, periods, and hyphens).
	 * 
	 * @param string $fileName The file name to check.
	 * @return bool True if the file name is valid, false otherwise.
	 */
	public static function isFilenameValid($fileName): bool {
		return (strlen($fileName) > 0) && (strlen($fileName) < 255) && !preg_match('/[^\p{L}\p{N} _\-.,()áéíóúÁÉÍÓÚüÜñÑ]|[\/:*?"<>|]|^( |\.)|^[ \.]+$|^(CON|PRN|AUX|NUL|COM[1-9]|LPT[1-9])$/i', $fileName);
	}

	/** 
	 * Upload file
	 * 
	 * @param array $file The file to upload ($_FILES['file'])
	 * @param string $destination The destination file
	 * @param int $maxFileSize The maximum allowed file size in MB. Default is taken from Configuration::MAX_UPLOAD_SIZE_MB.
	 * @param bool $overwrite If true, the file will be overwritten if it already exists, false by default
	 * @param array|null $allowedMimeTypes Allowed mime types, null by default (all types allowed)
	 * @param bool $ignoreUsageCap If true, the app usage cap will be ignored, false by default
	 * @return bool true if the file was uploaded successfully or throws an exception on failure.
	 * @throws SystemIOException if failed to upload file because of invalid file name, file type, file size, file already exists, or failed to move file
	 * @throws StorageException if the disk does not have the minimum required space or the app disk usage exceeded (if ignoreUsageCap is false)
	 */
	public static function uploadFile(array $file, string $destination, int $maxFileSize = Configuration::MAX_UPLOAD_SIZE_MB, bool $overwrite = false, ?array $allowedMimeTypes = null, bool $ignoreUsageCap = false): bool {
		if (!self::isMinimumDiskSpaceAvailable()) {
			throw new StorageException("Could not perform this action: Minimum disk space not available", self::ERR_NO_MINIMUM_STORAGE_SPACE);
		}
		if (!$ignoreUsageCap && !self::isAppUsedSpaceWithinLimits()) {
			throw new StorageException("Could not save file: App disk usage exceeded", self::ERR_APP_STORAGE_USAGE_EXCEEDED);
		}
		if ($file['error'] !== UPLOAD_ERR_OK) {
			throw new SystemIOException("Failed to upload file: '{$file['name']}'", self::ERR_FILE_NOT_UPLOADED);
		}
		if (!self::isFilenameValid($file['name'])) {
			throw new SystemIOException("Invalid file name: '{$file['name']}'", self::ERR_FILE_NAME_INVALID);
		}
		if ($allowedMimeTypes !== null && !in_array(self::getFileMimeType($file['tmp_name']), $allowedMimeTypes)) {
			throw new SystemIOException("Invalid file type: '{$file['name']}'", self::ERR_FILE_MIME_TYPE_INVALID);
		}
		if (self::getFileSize($file['tmp_name'], DataUnits::MEGABYTES) > $maxFileSize) {
			throw new SystemIOException("File size exceeds the maximum allowed size: '{$file['name']}'", self::ERR_FILE_EXEEDED_SIZE_LIMIT);
		}
		if (file_exists($destination) && !$overwrite) {
			throw new SystemIOException("File: '{$destination}' already exists.", self::ERR_FILE_ALREADY_EXISTS);
		}
		if (!move_uploaded_file($file['tmp_name'], $destination)) {
			throw new SystemIOException("Failed to upload file: '{$file}' to '{$destination}'", self::ERR_FILE_NOT_UPLOADED);
		} else {
			return true;
		}
	}

	/**
	 * Get a chunk of data from a file.
	 *
	 * @param string $fileName The path of the file.
	 * @param int $offset The offset from where to start reading the file.
	 * @param int $length The length of the chunk to read.
	 * @return string The chunk of data.
	 * @throws SystemIOException if failed to read the file.
	 */
	public static function getFileChunk($fileName, $offset, $length): string {
		$file = fopen($fileName, 'r');
		if (!$file) {
			throw new SystemIOException("Failed to open file: '{$fileName}'", self::ERR_FILE_NOT_OPENED);
		}
		fseek($file, $offset);
		$chunk = fread($file, $length);
		if ($chunk === false) {
			fclose($file);
			throw new SystemIOException("Failed to read file: '{$fileName}'", self::ERR_FILE_NOT_READ);
		}
		fclose($file);
		return $chunk;
	}
}
