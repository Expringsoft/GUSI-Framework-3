<?php
namespace App\Core\Framework\Classes;

use App\Core\Application\Configuration;
use App\Core\Application\SharedConsts;
use App\Core\Framework\Enumerables\DataUnits;
use App\Core\Server\Actions;
use App\Core\Server\FileManager;
use App\Core\Server\Logger;

/**
 * The ResourceManager class provides methods for loading and managing resources.
 */
class ResourceManager {

	public function __construct($Method, $args = []) {
		if (!isset($args['version']) || !isset($args['resource'])) {
			http_response_code(SharedConsts::HTTP_RESPONSE_BAD_REQUEST);
			echo "Bad Request";
			return;
		}
		if ($args['version'] != Configuration::APP_VERSION) {
			http_response_code(SharedConsts::HTTP_RESPONSE_NOT_FOUND);
			echo Actions::printLocalized(Strings::RESOURCE_NOT_FOUND);
			return;
		} else {
			$Resource = $args['resource'];
			$Resource = self::base_64_url_decode($Resource, true);
			if ($Resource === false) {
				http_response_code(SharedConsts::HTTP_RESPONSE_BAD_REQUEST);
				echo "Bad Request";;
				return;
			}
			if (strpos($Resource, '..') !== false || strpos($Resource, '/') === 0) {
				http_response_code(SharedConsts::HTTP_RESPONSE_FORBIDDEN);
				echo Actions::printLocalized(Strings::FORBIDDEN);
				return;
			}
			self::loadResource($Resource);
		}
	}

	/**
	 * Loads the specified resource file.
	 *
	 * @param string $path The path to the resource file.
	 * @return string The contents of the resource file.
	 */
	public static function loadResource(string $path)
	{
		$safePath = str_replace(['..', '\\', '/./', '/../'], '', $path);
		$safePath = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $safePath);
		$safePath = realpath(Configuration::RESOURCES_PATH . DIRECTORY_SEPARATOR . $safePath);
		if ($safePath === false || strpos($safePath, realpath(Configuration::RESOURCES_PATH)) !== 0) {
			http_response_code(SharedConsts::HTTP_RESPONSE_NOT_FOUND);
			echo Actions::printLocalized(Strings::RESOURCE_NOT_FOUND);
			return;
		}
		if (!file_exists($safePath)) {
			http_response_code(SharedConsts::HTTP_RESPONSE_NOT_FOUND);
			echo Actions::printLocalized(Strings::RESOURCE_NOT_FOUND);
			return;
		}
		if (FileManager::getFileSize($safePath, DataUnits::MEGABYTES) > Configuration::MAX_RESOURCE_SIZE_MB) {
			Logger::LogWarning(self::class, "Resource exceeds maximum size: {$safePath}");
			http_response_code(SharedConsts::HTTP_RESPONSE_FORBIDDEN);
			echo Actions::printLocalized(Strings::FORBIDDEN);
			return;
		}
		$excludedExtensions = ['php', 'php3', 'php4', 'php5', 'php7', 'phtml', 'phar', 'cgi', 'pl', 'py', 'rb', 'sh', 'bash', 'zsh', 'sh', 'csh', 'ksh', 'cmd', 'bat', 'com', 'exe', 'dll', 'vbs', 'jar', 'env'];
		$extension = pathinfo($safePath, PATHINFO_EXTENSION);
		if (in_array($extension, $excludedExtensions)) {
			Logger::LogWarning(self::class, "Attempted to access a restricted file: {$safePath}");
			http_response_code(SharedConsts::HTTP_RESPONSE_FORBIDDEN);
			echo Actions::printLocalized(Strings::FORBIDDEN);
			return;
		}
		$mimeTypes = [
			'jpg' => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'png' => 'image/png',
			'gif' => 'image/gif',
			'bmp' => 'image/bmp',
			'webp' => 'image/webp',
			'svg' => 'image/svg+xml',
			'ico' => 'image/x-icon',
			'css' => 'text/css',
			'js' => 'application/javascript',
			'woff' => 'application/font-woff',
			'woff2' => 'application/font-woff2',
			'ttf' => 'font/ttf',
			'otf' => 'font/otf',
			'eot' => 'application/vnd.ms-fontobject',
			'sfnt' => 'font/sfnt',
			'html' => 'text/html',
			'htm' => 'text/html',
			'pdf' => 'application/pdf',
			'txt' => 'text/plain',
			'xml' => 'application/xml',
			'mp4' => 'video/mp4',
			'webm' => 'video/webm',
			'ogg' => ['audio/ogg', 'video/ogg'],
			'avi' => 'video/x-msvideo',
			'mp3' => 'audio/mpeg',
			'wav' => 'audio/wav',
			'm4a' => 'audio/mp4',
			'zip' => 'application/zip',
			'rar' => 'application/x-rar-compressed',
			'7z' => 'application/x-7z-compressed',
			'tar' => 'application/x-tar',
			'gz' => 'application/gzip',
			'json' => 'application/json',
			'csv' => 'text/csv',
			'doc' => 'application/msword',
			'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'xls' => 'application/vnd.ms-excel',
			'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'ppt' => 'application/vnd.ms-powerpoint',
			'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
			'apk' => 'application/vnd.android.package-archive',
			'exe' => 'application/octet-stream',
			'bin' => 'application/octet-stream',
			'dmg' => 'application/x-apple-diskimage',
			'iso' => 'application/x-iso9660-image',
		];
		if (in_array($extension, array_keys($mimeTypes))) {
			$mimeType = $mimeTypes[$extension];
		} else {
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$mimeType = finfo_file($finfo, $safePath);
			finfo_close($finfo);;
		}
		header('Content-Type: ' . $mimeType);
		header('Content-Length: ' . filesize($safePath));
		header('Cache-Control: public, max-age=' . Configuration::RESOURCE_CACHE_TIME);
		header('Expires: ' . gmdate('D, d M Y H:i:s', time() + Configuration::RESOURCE_CACHE_TIME) . ' GMT');
		header('Pragma: public');
		header('Fetch-As: ' . $extension);
		header('GF-Name: ' . pathinfo($safePath, PATHINFO_BASENAME));
		header('X-Content-Type-Options: nosniff');
		header('X-Frame-Options: DENY');
		header('X-XSS-Protection: 1; mode=block');
		readfile($safePath);
	}

	public static function base_64_url_encode($input) {
		return strtr(base64_encode($input), '+/=', '-_,');
	}

	public static function base_64_url_decode($input) {
		return base64_decode(strtr($input, '-_,', '+/='));
	}
}