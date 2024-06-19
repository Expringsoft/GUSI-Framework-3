<?php

namespace App\Core\Server;

use App\Core\Application\Configuration;
use App\Core\Framework\Abstracts\SingletonInstance;
use InvalidArgumentException;

/**
 * Class Router
 * 
 * The Router class handles the routing of incoming requests to the appropriate controller and action.
 */
class Router extends SingletonInstance
{
	protected $baseUrl;

	protected $routes = array();

	protected $parameters = ['GET' => array(), 'POST' => array(), 'PATH_SEGMENTS' => array()];

	/**
	 * Router constructor.
	 * 
	 * Initializes the Router object.
	 */
	public function __construct()
	{
		$this->setBaseUrl();
		$this->createRequest();
	}

	/**
	 * Checks if the context is secure (running over https).
	 *
	 * @return bool True if the context is secure, false otherwise.
	 */
	public static function isContextSecure(): bool
	{
		return isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === '1');
	}

	/**
	 * Adds a route to the router.
	 * 
	 * @param string $route The route.
	 * @param string|array $controller If a string, the controller name (Example::class). If an array, the controller name and method ([Example::class, 'Main']). If method is not specified, it defaults to 'Main'.
	 * 
	 * @return $this
	 */
	public function addRoute($route, $controller)
	{
		if (isset($this->routes[$route])) {
			Logger::LogWarning(self::class, "Route '{$route}' has been overwritten.");
		}

		if (is_string($controller)) {
			$controller = [$controller, 'Main'];
		} elseif (is_array($controller) && count($controller) == 1) {
			$controller[] = 'Main';
		} elseif (!is_array($controller) || count($controller) != 2) {
			throw new InvalidArgumentException("The controller must be a string or an array with one or two elements: the controller name and the method.");
		}

		$this->routes[$route] = $controller;
		return $this;
	}

	/**
	 * Sets the base URL for the application.
	 * 
	 * If the application is running in a local environment, the base URL is set to the value specified in the Configuration class.
	 * Otherwise, the base URL is set to the current server's name.
	 * 
	 * @return $this
	 */
	public function setBaseUrl()
	{
		if (Configuration::LOCAL_ENVIRONMENT) {
			$this->baseUrl = Configuration::PATH_URL;
		} else {
			$this->baseUrl = "{$_SERVER['SERVER_NAME']}/";
		}
		return $this;
	}

	/**
	 * Obtains the operating system from the user agent.
	 *
	 * @param string|null $UserAgent The user agent string to parse. If null, the user agent from the request will be used.
	 * @return string The operating system from the user agent. Unknown if not found.
	 */
	public static function getOSFromUserAgent(string $UserAgent = null)
	{
		$os = "Unknown";
		$user_agent = null;

		if ($UserAgent == null) {
			if (!isset($_SERVER['HTTP_USER_AGENT'])) {
				return $os;
			}
			$user_agent = $_SERVER['HTTP_USER_AGENT'];
		} else {
			$user_agent = $UserAgent;
		}

		$os_array = array(
			'/googlebot-mobile/i'					=> 'Googlebot Mobile',
			'/googlebot/i'							=> 'Googlebot',
			'/bingbot/i'							=> 'Bingbot',
			'/msnbot/i'								=> 'MSNbot',
			'/grapeshotcrawler/i'					=> 'Grapeshot Crawler Bot',
			'/yandexbot/i'							=> 'Yandexbot',
			'/baiduspider/i'						=> 'Baiduspider Bot',
			'/duckduckbot/i'						=> 'DuckDuckGo Bot',
			'/duckassistbot/i'						=> 'DuckAssistBot',
			'/facebookexternalhit/i'				=> 'Facebook Bot (External Hit)',
			'/facebookbot/i'						=> 'Facebook Bot',
			'/telegrambot/i'						=> 'Telegram Bot',
			'/twitterbot/i'							=> 'Twitter Bot',
			'/discordbot/i'							=> 'Discord Bot',
			'/linkedinbot/i'						=> 'LinkedIn Bot',
			'/pinterestbot/i'						=> 'Pinterest Bot',
			'/slackbot-linkexpanding/i'				=> 'Slack Bot (Link Expanding)',
			'/slackbot-linkpreview/i'				=> 'Slack Bot (Link Preview)',
			'/slackbot/i'							=> 'Slack Bot',
			'/applebot/i'							=> 'Apple Bot',
			'/yahoo! slurp/i'						=> 'Yahoo! Slurp Bot',
			'/ia_archiver/i'						=> 'Alexa Bot',
			'/archive.org_bot/i'					=> 'Archive.org Bot',
			'/adobeair/i'							=> 'Adobe AIR',
			'/windows phone(?: ([0-9.,_]+))?/i'		=> 'Windows Phone$1',
			'/windows nt 10/i'						=> 'Windows 10',
			'/windows nt 6.3/i'						=> 'Windows 8.1',
			'/windows nt 6.2/i'						=> 'Windows 8',
			'/windows nt 6.1/i'						=> 'Windows 7',
			'/windows nt 6.0/i'						=> 'Windows Vista',
			'/windows nt 5.2/i'						=> 'Windows Server 2003/XP x64',
			'/windows nt 5.1/i'						=> 'Windows XP',
			'/windows xp/i'							=> 'Windows XP',
			'/windows nt 5.0/i'						=> 'Windows 2000',
			'/windows me/i'							=> 'Windows ME',
			'/win98/i'								=> 'Windows 98',
			'/win95/i'								=> 'Windows 95',
			'/win16/i'								=> 'Windows 3.11',
			'/iphone(?: ([0-9.,_]+))?/i'			=> 'iPhone$1',
			'/macintosh|mac os x/i'					=> 'Mac OS X',
			'/mac_powerpc/i'						=> 'Mac OS 9',
			'/cros x86_64/i'						=> 'Chrome OS x64',
			'/cros armv7l/i'						=> 'Chrome OS ARM',
			'/cros aarch64/i'						=> 'Chrome OS ARM64',
			'/android(?: ([0-9.]+))?/i'				=> 'Android$1',
			'/freebsd/i'							=> 'FreeBSD',
			'/linux/i'								=> 'Linux',
			'/ubuntu/i'								=> 'Ubuntu',
			'/ipod/i'								=> 'iPod',
			'/ipad/i'								=> 'iPad',
			'/blackberry/i'							=> 'BlackBerry',
			'/webos/i'								=> 'Mobile'
		);

		foreach ($os_array as $regex => $value) {
			if (preg_match($regex, $user_agent, $matches)) {
				$os = str_replace('$1', isset($matches[1]) ? ' ' . $matches[1] : '', $value);
				break;
			}
		}

		if ($os == "Windows 10" && isset($_SERVER['HTTP_SEC_CH_UA_PLATFORM_VERSION'])) {
			if (version_compare($_SERVER['HTTP_SEC_CH_UA_PLATFORM_VERSION'], "13", ">=")) {
				$os = "Windows 11";
			}
		}

		return $os;
	}

	/**
	 * Obtains the browser from the user agent.
	 *
	 * @param string|null $UserAgent The user agent string to parse. If null, the user agent from the request will be used.
	 * @return string The browser from the user agent. Unknown if not found.
	 */
	public static function getBrowserFromUserAgent(string $UserAgent = null)
	{
		$browser = "Unknown";
		$user_agent = null;

		if ($UserAgent == null) {
			if (!isset($_SERVER['HTTP_USER_AGENT'])) {
				return "Unknown";
			}
			$user_agent = $_SERVER['HTTP_USER_AGENT'];
		} else {
			$user_agent = $UserAgent;
		}

		$browser_array = array(
			'/postmanruntime/i'	=> 'Postman API Platform',
			'/trident\/4.0/i'	=> 'Internet Explorer 8',
			'/trident\/5.0/i'	=> 'Internet Explorer 9',
			'/trident\/6.0/i'	=> 'Internet Explorer 10',
			'/trident\/7.0/i'	=> 'Internet Explorer 11',
			'/trident/i'		=> 'Internet Explorer',
			'/msie/i'			=> 'Internet Explorer',
			'/duckduckgo/i'		=> 'DuckDuckGo',
			'/edg/i'			=> 'Microsoft Edge',
			'/edge/i'			=> 'Microsoft Edge',
			'/msedge/i'			=> 'Microsoft Edge',
			'/firefox/i'		=> 'Mozilla Firefox',
			'/opera/i'			=> 'Opera',
			'/opr/i'			=> 'Opera',
			'/origin/i'			=> 'EA Origin',
			'/netscape/i'		=> 'Netscape',
			'/maxthon/i'		=> 'Maxthon',
			'/konqueror/i'		=> 'Konqueror',
			'/brave/i'			=> 'Brave',
			'/vivaldi/i'		=> 'Vivaldi',
			'/yabrowser/i'		=> 'Yandex',
			'/yowser/i'			=> 'Yandex',
			'/samsungbrowser/i'	=> 'Samsung Internet',
			'/epic/i'			=> 'Epic',
			'/maxthon/i'		=> 'Maxthon',
			'/ucbrowser/i'		=> 'UC Browser',
			'/chrome/i'			=> 'Chrome',
			'/safari/i'			=> 'Safari',
			'/mobile/i'			=> 'Mobile Device',
		);

		foreach ($browser_array as $regex => $value) {
			if (preg_match($regex, $user_agent)) {
				$browser = $value;
				break;
			}
		}

		return $browser;
	}

	/**
	 * Obtains the user's IP address.
	 *
	 * @return string The user's IP address.
	 */
	public static function getIPAddress()
	{
		try {
			// Get the user's IP address
			$ip_address = $_SERVER['REMOTE_ADDR'];

			// Check for shared internet
			if (array_key_exists('HTTP_CLIENT_IP', $_SERVER)) {
				$ip_address = $_SERVER['HTTP_CLIENT_IP'];
			} else if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
				$ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else if (array_key_exists('HTTP_X_FORWARDED', $_SERVER)) {
				$ip_address = $_SERVER['HTTP_X_FORWARDED'];
			} else if (array_key_exists('HTTP_FORWARDED_FOR', $_SERVER)) {
				$ip_address = $_SERVER['HTTP_FORWARDED_FOR'];
			} else if (array_key_exists('HTTP_FORWARDED', $_SERVER)) {
				$ip_address = $_SERVER['HTTP_FORWARDED'];
			} else if (array_key_exists('HTTP_X_CLUSTER_CLIENT_IP', $_SERVER)) {
				$ip_address = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
			} else if (array_key_exists('HTTP_FORWARDED_FOR_IP', $_SERVER)) {
				$ip_address = $_SERVER['HTTP_FORWARDED_FOR_IP'];
			} else if (array_key_exists('HTTP_FORWARDED_IP', $_SERVER)) {
				$ip_address = $_SERVER['HTTP_FORWARDED_IP'];
			}

			return $ip_address;
		} catch (\Exception $e) {
			return null;
		}
	}

	/**
	 * Obtains the user's language.
	 *
	 * @return string The user's language.
	 */
	public static function getUserLanguage()
	{
		$language = 'default';

		if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			$langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
			if (count($langs) > 0) {
				$language = $langs[0];
			}
		}

		return $language;
	}


	/**
	 * Returns the base URL of the server.
	 *
	 * @return string The base URL of the server.
	 */
	public function getBaseUrl()
	{
		return Configuration::LOCAL_ENVIRONMENT ? $_SERVER['SERVER_NAME'] . $this->baseUrl : $this->baseUrl;
	}

	/**
	 * Sets the parameters for the request.
	 * 
	 * @param array $params The parameters.
	 * 
	 * @return $this
	 */
	public function setParameters($params)
	{
		$this->parameters = $params;
		return $this;
	}

	/**
	 * Returns the parameters for the request.
	 * 
	 * @return array The parameters.
	 */
	public function getParameters()
	{
		if ($this->parameters == null) {
			$this->parameters = array();
		}
		return $this->parameters;
	}

	/**
	 * Returns the full path of the request.
	 * 
	 * @return string Full request path.
	 */
	public function getFormattedPathSegments()
	{
		$params = $this->getParameters();
		if (isset($params['PATH_SEGMENTS'])) {
			$routePath = "";
			if (sizeof($params['PATH_SEGMENTS']) > 0 && sizeof($params['PATH_SEGMENTS']) < 32) {
				foreach ($params['PATH_SEGMENTS'] as $Route) {
					$routePath .= $Route . "/";
				}
				$routePath = rtrim($routePath, "/");
			}
			return $routePath;
		} else {
			return "";
		}
	}

	/**
	 * Returns the controller class name which is the first segment of the request URI.
	 * 
	 * @return string The controller class name.
	 */
	public function getControllerClassName()
	{
		return $this->parameters['PATH_SEGMENTS'][0];
	}

	/**
	 * Get the request URI.
	 *
	 * @return string The request URI.
	 */
	public function getRequestUri()
	{
		if (!isset($_SERVER['REQUEST_URI'])) {
			return '';
		}
		$uri = $_SERVER['REQUEST_URI'];
		// TODO: Improve this temporary code to avoid situations of // or //// or more
		// at the beginning and end of the URL.
		if (strpos($uri, '/') === 0) {
			$uri = substr($uri, 1);
		}
		if (strrpos($uri, '/') === (strlen($uri) - 1)) {
			$uri = substr($uri, 0, -1);
		}
		return $uri;
	}

	/**
	 * Create a request object.
	 * 
	 * @return $this
	 */
	public function createRequest()
	{
		// Store the request URI
		$uri = $this->getRequestUri();
		// Initial item in the URI parts array is /
		$uriParts = ["/"];
		// Split the URI into parts
		$uriPath = explode('/', $uri);
		// The root path / is lost when splitting the URI; add it back if the first part is not empty
		if ($uriPath[0] != "") {
			$uriParts = array_merge($uriParts, $uriPath);
		}

		// Retrieve data received via JSON (e.g., in POST requests)
		$jsonData = json_decode(file_get_contents("php://input"), true);

		// Create an empty array if there is no JSON data
		if (!is_array($jsonData)) {
			$jsonData = [];
		}

		// Create an empty array if there is no POST data
		if (!is_array($_POST)) {
			$_POST = [];
		}

		// Merge JSON data and POST data
		$this->parameters['POST'] = array_merge($_POST, $jsonData);

		// Add query parameters to the GET parameters array
		$queryString = parse_url($uri, PHP_URL_QUERY);
		if ($queryString) {
			parse_str($queryString, $queryParams);
			foreach ($queryParams as $key => $value) {
				$this->parameters['GET'][$key] = $value;
			}
		}

		// Store URI path segments without query parameters
		$this->parameters['PATH_SEGMENTS'] = [];
		foreach ($uriParts as $part) {
			$value = explode('?', $part)[0]; // Retrieve the value before '?'
			$this->parameters['PATH_SEGMENTS'][] = $value;
		}

		return $this;
	}

	/**
	 * Handles the incoming request.
	 * 
	 * @return void
	 */
	public function handleRequest()
	{
		// Iterate through Module-defined routes
		foreach ($this->routes as $route => $controllerAndMethod) {
			// Split Module-defined paths by / to compare each segment with the requested route segments
			$routeParts = explode('/', $route);
			// The first part of the route is always /
			$routeParts[0] = "/";
			// Set the route segments to / if the route is /, otherwise use the route parts
			$routeSegments = $route === "/" ? ["/"] : $routeParts;

			// Continue to the next route if the number of segments in the requested route 
			// does not match the number of segments in the route
			if (count($routeSegments) != count($this->parameters['PATH_SEGMENTS'])) {
				continue;
			}

			// Initialize the parameters array
			$parameters = [];
			// Iterate through the route segments
			for ($i = 0; $i < count($routeSegments); $i++) {
				// Add to the parameters array if the route segment is a parameter defined by param name enclosed with {}
				if ($routeSegments[$i][0] == '{' && $routeSegments[$i][-1] == '}') {
					$parameters[trim($routeSegments[$i], '{}')] = $this->parameters['PATH_SEGMENTS'][$i];
				} elseif ($routeSegments[$i] != $this->parameters['PATH_SEGMENTS'][$i]) {
					// Continue to the next route if the route segment does not match the requested route segment
					continue 2;
				}
			}

			// At this point, the route matches the requested route; create a new instance of 
			// the controller and call the method with the parameters array, 0 is the controller class and 1 is the method.
			$controller = new $controllerAndMethod[0]($controllerAndMethod[1], $parameters);
			return;
		}

		// Render a 404 page if no route matches the requested route
		Actions::renderNotFound();
	}
}
