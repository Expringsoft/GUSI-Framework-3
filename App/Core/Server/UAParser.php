<?php

namespace App\Core\Server;

class UAParser
{
	private const UNKNOWN = 'Unknown';

	/**
	 * Array de patrones para detectar sistemas operativos.
	 */
	private static $os_array = [
		// Xbox (Detected as Windows 10)
		'/xbox series x/i'						=> 'Xbox Series X',
		'/xbox series s/i'						=> 'Xbox Series S',
		'/xbox_one_ed/i'						=> 'Xbox One S',
		'/xbox one/i'							=> 'Xbox One',
		// Windows
		'/windows nt 10.0/i'                    => 'Windows 10',
		'/windows nt 10.0; win64; x64/i'        => 'Windows 10',
		'/windows nt 6.3/i'                     => 'Windows 8.1',
		'/windows nt 6.2/i'                     => 'Windows 8',
		'/windows nt 6.1/i'                     => 'Windows 7',
		'/windows nt 6.0/i'                     => 'Windows Vista',
		'/windows nt 5.2/i'                     => 'Windows Server 2003/XP x64',
		'/windows nt 5.1/i'                     => 'Windows XP',
		'/windows xp/i'                         => 'Windows XP',
		'/windows nt 5.0/i'                     => 'Windows 2000',
		'/windows me/i'                         => 'Windows ME',
		'/win98/i'                              => 'Windows 98',
		'/win95/i'                              => 'Windows 95',
		'/win16/i'                              => 'Windows 3.11',
		'/windows phone(?: os)? ([0-9.]+)/i'    => 'Windows Phone $1',
		// Apple
		'/iphone os ([0-9_]+)/i'                => 'iOS $1',
		'/ipad.*os ([0-9_]+)/i'                 => 'iPadOS $1',
		'/mac os x ([0-9_]+)/i'                 => 'Mac OS X $1',
		'/macintosh|mac os x/i'                 => 'Mac OS X',
		'/mac_powerpc/i'                        => 'Mac OS 9',
		// Android
		'/android ([0-9.]+)/i'                  => 'Android $1',
		'/android/i'                            => 'Android',
		// Linux
		'/linux/i'                              => 'Linux',
		'/ubuntu/i'                             => 'Ubuntu',
		'/debian/i'                             => 'Debian',
		'/fedora/i'                             => 'Fedora',
		'/centos/i'                             => 'CentOS',
		'/freebsd/i'                            => 'FreeBSD',
		// Otros
		'/playstation; playstation 5/i'			=> 'PlayStation 5',
		'/playstation 4/i'						=> 'PlayStation 4',
		'/playstation vita/i'					=> 'PlayStation Vita',
		'/nintendo switch/i'					=> 'Nintendo Switch',
		'/nintendo wiiu/i'						=> 'Nintendo Wii U',
		'/nintendo 3ds/i'						=> 'Nintendo 3DS',
		'/appletv([0-9,]+)/i'					=> 'AppleTV $1',
		'/blackberry/i'                         => 'BlackBerry',
		'/webos/i'                              => 'Mobile',
		'/cros/i'                               => 'Chrome OS',
		'/symbianos/i'                          => 'Symbian OS',
		'/nokia/i'                              => 'Nokia',
		'/bb10/i'                               => 'BlackBerry 10',
	];

	/**
	 * Array de patrones para detectar navegadores.
	 */
	private static $browser_array = [
		'/msie ([0-9.]+)/i'                     => 'Internet Explorer $1',
		'/trident\/.*rv:([0-9.]+)/i'            => 'Internet Explorer $1',
		'/edge\/([0-9.]+)/i'                    => 'Microsoft Edge $1',
		'/edg\/([0-9.]+)/i'                     => 'Microsoft Edge $1',
		'/firefox\/([0-9.]+)/i'                 => 'Mozilla Firefox $1',
		'/opr\/([0-9.]+)/i'                     => 'Opera $1',
		'/opera\/([0-9.]+)/i'                   => 'Opera $1',
		'/chrome\/([0-9.]+)/i'                  => 'Chrome $1',
		'/safari\/([0-9.]+)/i'                  => 'Safari $1',
		'/brave\/([0-9.]+)/i'                   => 'Brave $1',
		'/vivaldi\/([0-9.]+)/i'                 => 'Vivaldi $1',
		'/yabrowser\/([0-9.]+)/i'               => 'Yandex Browser $1',
		'/duckduckgo\/([0-9.]+)/i'              => 'DuckDuckGo $1',
		'/postmanruntime\/([0-9.]+)/i'          => 'PostmanRuntime $1',
		'/ucbrowser\/([0-9.]+)/i'               => 'UC Browser $1',
		'/samsungbrowser\/([0-9.]+)/i'          => 'Samsung Internet $1',
		'/qqbrowser\/([0-9.]+)/i'               => 'QQ Browser $1',
		'/baidubrowser\/([0-9.]+)/i'            => 'Baidu Browser $1',
		'/maxthon\/([0-9.]+)/i'                 => 'Maxthon $1',
		'/konqueror\/([0-9.]+)/i'               => 'Konqueror $1',
		'/netscape\/([0-9.]+)/i'                => 'Netscape $1',
		'/silk\/([0-9.]+)/i'                    => 'Silk $1',
		'/midori\/([0-9.]+)/i'                  => 'Midori $1',
		'/chromium\/([0-9.]+)/i'                => 'Chromium $1',
		'/bolt\/([0-9.]+)/i'                    => 'Bolt $1',
		'/iron\/([0-9.]+)/i'                    => 'Iron Browser $1',
		'/tor browser\/([0-9.]+)/i'             => 'Tor Browser $1',
		'/palemoon\/([0-9.]+)/i'                => 'Pale Moon $1',
		'/waterfox\/([0-9.]+)/i'                => 'Waterfox $1',
		// Otros
		'/mobile/i'                             => 'Mobile Device',
		'/tablet/i'                             => 'Tablet',
	];

	private static $bot_array = [
		'/googlebot-mobile/i'       => 'Googlebot Mobile',
		'/googlebot/i'              => 'Googlebot',
		'/bingbot/i'                => 'Bingbot',
		'/msnbot/i'                 => 'MSNbot',
		'/grapeshotcrawler/i'       => 'Grapeshot Crawler Bot',
		'/yandexbot/i'              => 'Yandexbot',
		'/baiduspider/i'            => 'Baiduspider Bot',
		'/duckduckbot/i'            => 'DuckDuckGo Bot',
		'/duckassistbot/i'          => 'DuckAssistBot',
		'/facebookexternalhit/i'    => 'Facebook Bot (External Hit)',
		'/facebookbot/i'            => 'Facebook Bot',
		'/telegrambot/i'            => 'Telegram Bot',
		'/twitterbot/i'             => 'Twitter Bot',
		'/discordbot/i'             => 'Discord Bot',
		'/linkedinbot/i'            => 'LinkedIn Bot',
		'/pinterestbot/i'           => 'Pinterest Bot',
		'/slackbot/i'               => 'Slack Bot',
		'/applebot/i'               => 'Apple Bot',
		'/yahoo! slurp/i'           => 'Yahoo! Slurp Bot',
		'/ia_archiver/i'            => 'Alexa Bot',
		'/archive.org_bot/i'        => 'Archive.org Bot',
	];

	/**
	 * Método privado para analizar el user agent usando patrones definidos.
	 *
	 * @param string $userAgent El user agent a analizar.
	 * @param array $patterns Los patrones para buscar coincidencias.
	 * @param string $default Valor por defecto si no hay coincidencias.
	 * @return string Resultado del análisis.
	 */
	private static function parseUserAgent(string $userAgent, array $patterns, string $default): string
	{
		foreach ($patterns as $regex => $value) {
			if (preg_match($regex, $userAgent, $matches)) {
				$result = $value;
				if (strpos($value, '$1') !== false && isset($matches[1])) {
					// Reemplaza $1 con la versión capturada y normaliza el formato
					$version = str_replace('_', '.', $matches[1]);
					$result = str_replace('$1', $version, $value);
				}
				return $result;
			}
		}
		return $default;
	}

	/**
	 * Obtiene el sistema operativo del user agent.
	 *
	 * @param string|null $userAgent El user agent a analizar. Si es null, se usa el de la solicitud.
	 * @return string El sistema operativo identificado.
	 */
	public static function getOSFromUserAgent(string $userAgent = null): string
	{
		$userAgent = $userAgent ?? $_SERVER['HTTP_USER_AGENT'] ?? self::UNKNOWN;
		$os = self::parseUserAgent($userAgent, array_merge(self::$os_array, self::$bot_array), self::UNKNOWN);

		// Especificar Windows 11 si se detecta mediante User-Agent Client Hints
		if ($os === 'Windows 10' && isset($_SERVER['HTTP_SEC_CH_UA_PLATFORM_VERSION'])) {
			$platformVersion = trim($_SERVER['HTTP_SEC_CH_UA_PLATFORM_VERSION'], '"');
			if (version_compare($platformVersion, '13.0.0', '>=')) {
				$os = 'Windows 11';
			}
		}

		// Normalizar versiones de iOS y Mac OS X
		$os = preg_replace_callback('/(iOS|Mac OS X) ([0-9.]+)/', function ($matches) {
			$version = str_replace('_', '.', $matches[2]);
			return $matches[1] . ' ' . $version;
		}, $os);

		return $os;
	}

	/**
	 * Obtiene el navegador del user agent.
	 *
	 * @param string|null $userAgent El user agent a analizar. Si es null, se usa el de la solicitud.
	 * @return string El navegador identificado.
	 */
	public static function getBrowserFromUserAgent(string $userAgent = null): string
	{
		$userAgent = $userAgent ?? $_SERVER['HTTP_USER_AGENT'] ?? self::UNKNOWN;
		return self::parseUserAgent($userAgent, self::$browser_array, self::UNKNOWN);
	}

	/**
	 * Verifica si el user agent corresponde a un bot.
	 *
	 * @param string|null $userAgent El user agent a analizar. Si es null, se usa el de la solicitud.
	 * @return bool True si es un bot, false en caso contrario.
	 */
	public static function isBot(string $userAgent = null): bool
	{
		$userAgent = $userAgent ?? $_SERVER['HTTP_USER_AGENT'] ?? self::UNKNOWN;
		if (self::parseUserAgent($userAgent, self::$bot_array, self::UNKNOWN) != self::UNKNOWN) {
			return true;
		}
		return false;
	}

	/**
	 * Obtiene el navegador a partir de los Client Hints.
	 *
	 * @return string El navegador identificado.
	 */
	public static function getBrowserFromClientHints(): string
	{
		$browserHints = $_SERVER['HTTP_SEC_CH_UA'] ?? '';
		if ($browserHints) {
			// Extrae los nombres de los navegadores
			preg_match_all('/"([^"]+)"/', $browserHints, $matches);
			if (!empty($matches[1])) {
				foreach ($matches[1] as $browserName) {
					// Ignora los valores genéricos
					if (!in_array($browserName, ['Not A;Brand', 'Not)A;Brand', 'Chromium'])) {
						return $browserName;
					}
				}
				return $matches[1][0]; // Retorna el primer valor si no se encontró otro
			}
		}
		return self::UNKNOWN;
	}

	/**
	 * Obtiene el sistema operativo a partir de los Client Hints.
	 *
	 * @return string El sistema operativo identificado.
	 */
	public static function getOSFromClientHints(): string
	{
		$platform = $_SERVER['HTTP_SEC_CH_UA_PLATFORM'] ?? '';
		if ($platform) {
			return trim($platform, '"');
		}
		return self::UNKNOWN;
	}
}