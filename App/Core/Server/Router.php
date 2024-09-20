<?php

namespace App\Core\Server;

use App\Core\Application\Configuration;
use App\Core\Framework\Abstracts\SingletonInstance;
use InvalidArgumentException;
use PSpell\Config;

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
		$Protocol = self::isContextSecure() ? "https://" : "http://";
		if(Configuration::LOCAL_ENVIRONMENT){
			$this->baseUrl = $Protocol . $_SERVER['SERVER_NAME'] . Configuration::PATH_URL;
		} else {
			$this->baseUrl = $Protocol . Configuration::APP_DOMAIN;
		}
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

		if ($UserAgent === null) {
			$UserAgent = $_SERVER['HTTP_USER_AGENT'] ?? $os;
		}

		$os_array = array(
			// Bots
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
			// Windows
			'/windows nt 10/i'          => 'Windows 10',
			'/windows nt 6.3/i'         => 'Windows 8.1',
			'/windows nt 6.2/i'         => 'Windows 8',
			'/windows nt 6.1/i'         => 'Windows 7',
			'/windows nt 6.0/i'         => 'Windows Vista',
			'/windows nt 5.2/i'         => 'Windows Server 2003/XP x64',
			'/windows nt 5.1/i'         => 'Windows XP',
			'/windows xp/i'             => 'Windows XP',
			'/windows nt 5.0/i'         => 'Windows 2000',
			'/windows me/i'             => 'Windows ME',
			'/win98/i'                  => 'Windows 98',
			'/win95/i'                  => 'Windows 95',
			'/win16/i'                  => 'Windows 3.11',
			'/windows phone(?: ([0-9.,_]+))?/i' => 'Windows Phone$1',
			// Apple
			'/iphone(?: ([0-9.,_]+))?/i' => 'iPhone$1',
			'/ipad/i'                   => 'iPad',
			'/ipod/i'                   => 'iPod',
			'/macintosh|mac os x 10_15/i' => 'macOS Catalina',
			'/mac os x 10_16|mac os x 11/i' => 'macOS Big Sur',
			'/mac os x 12/i'            => 'macOS Monterey',
			'/mac os x 13/i'            => 'macOS Ventura',
			'/macintosh|mac os x/i'     => 'Mac OS X',
			'/mac_powerpc/i'            => 'Mac OS 9',
			// Android and Chrome OS
			'/android(?: ([0-9.]+))?/i' => 'Android$1',
			'/cros x86_64/i'            => 'Chrome OS x64',
			'/cros armv7l/i'            => 'Chrome OS ARM',
			'/cros aarch64/i'           => 'Chrome OS ARM64',
			// Linux and other Unix-like OS
			'/ubuntu/i'                 => 'Ubuntu',
			'/freebsd/i'                => 'FreeBSD',
			'/linux/i'                  => 'Linux',
			'/debian/i'                 => 'Debian',
			'/centos/i'                 => 'CentOS',
			'/fedora/i'                 => 'Fedora',
			'/blackberry/i'             => 'BlackBerry',
			'/webos/i'                  => 'Mobile',
			// Others
			'/adobeair/i'               => 'Adobe AIR'
		);

		foreach ($os_array as $regex => $value) {
			if (preg_match($regex, $UserAgent, $matches)) {
				$os = str_replace('$1', isset($matches[1]) ? ' ' . $matches[1] : '', $value);
				break;
			}
		}

		// Especificar Windows 11 si se detecta a través del User-Agent Client Hints
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
		if ($UserAgent === null) {
			$UserAgent = $_SERVER['HTTP_USER_AGENT'] ?? "Unknown";
		}
		$browser = "Unknown";

		$browser_array = array(
			'/postmanruntime/i'     => 'Postman API Platform',
			'/trident\/7.0/i'       => 'Internet Explorer 11',
			'/trident\/6.0/i'       => 'Internet Explorer 10',
			'/trident\/5.0/i'       => 'Internet Explorer 9',
			'/trident\/4.0/i'       => 'Internet Explorer 8',
			'/trident/i'            => 'Internet Explorer',
			'/msie/i'               => 'Internet Explorer',
			'/duckduckgo/i'         => 'DuckDuckGo',
			'/edg/i'                => 'Microsoft Edge',
			'/msedge/i'             => 'Microsoft Edge',
			'/firefox/i'            => 'Mozilla Firefox',
			'/opr\/gx/i'            => 'Opera GX',
			'/opr/i'                => 'Opera',
			'/opera/i'              => 'Opera',
			'/origin/i'             => 'EA Origin',
			'/netscape/i'           => 'Netscape',
			'/maxthon/i'            => 'Maxthon',
			'/konqueror/i'          => 'Konqueror',
			'/brave/i'              => 'Brave',
			'/vivaldi/i'            => 'Vivaldi',
			'/yabrowser/i'          => 'Yandex',
			'/yowser/i'             => 'Yandex',
			'/samsungbrowser/i'     => 'Samsung Internet',
			'/epic/i'               => 'Epic',
			'/ucbrowser/i'          => 'UC Browser',
			'/qqbrowser/i'          => 'QQ Browser',
			'/baidubrowser/i'       => 'Baidu Browser',
			'/palemoon/i'           => 'Pale Moon',
			'/waterfox/i'           => 'Waterfox',
			'/torbrowser/i'         => 'Tor Browser',
			'/chromium/i'           => 'Chromium',
			'/chrome/i'             => 'Chrome',
			'/safari/i'             => 'Safari',
			'/mobile/i'             => 'Mobile Device',
		);

		foreach ($browser_array as $regex => $value) {
			if (preg_match($regex, $UserAgent)) {
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
			// Check if the REMOTE_ADDR is set
			if (!isset($_SERVER['REMOTE_ADDR'])) {
				return null;
			}

			// Get initial IP from REMOTE_ADDR
			$ip_address = $_SERVER['REMOTE_ADDR'];

			// List of possible headers that may contain the real IP address
			$possible_headers = [
				'HTTP_CLIENT_IP',
				'HTTP_X_FORWARDED_FOR',
				'HTTP_X_FORWARDED',
				'HTTP_FORWARDED_FOR',
				'HTTP_FORWARDED',
				'HTTP_X_CLUSTER_CLIENT_IP',
				'HTTP_FORWARDED_FOR_IP',
				'HTTP_FORWARDED_IP'
			];

			foreach ($possible_headers as $header) {
				if (array_key_exists($header, $_SERVER) && filter_var($_SERVER[$header], FILTER_VALIDATE_IP)) {
					$ip_address = $_SERVER[$header];
					break; // Break the loop if a valid IP is found
				}
			}

			// Return the IP address if it is valid
			if (filter_var($ip_address, FILTER_VALIDATE_IP)) {
				return $ip_address;
			}

			return null;
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
		return $this->baseUrl;
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
				// Check if the segment is initialized before accessing it
				if (!isset($routeSegments[$i][0]) || !isset($routeSegments[$i][-1])) {
					continue 2; // Skip to the next route if the segment is not initialized
				}
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

	/**
	 * Generates the router map YAML file.
	 *
	 * @return void
	 */
	public function generateRoutermap()
	{
		$groupedRoutes = [];

		// Agrupar rutas por controlador
		foreach ($this->routes as $route => $controller) {
			$controllerName = $controller[0];
			$methodName = $controller[1];
			if (!isset($groupedRoutes[$controllerName])) {
				$groupedRoutes[$controllerName] = [];
			}
			$groupedRoutes[$controllerName][] = ['route' => $route, 'method' => $methodName];
		}

		// Generar contenido YAML
		$yamlContent = "Routermap:\n\n";
		foreach ($groupedRoutes as $controller => $routes) {
			$yamlContent .= "  {$controller}:\n";
			foreach ($routes as $routeInfo) {
				$yamlContent .= "    - route: {$routeInfo['route']} # {$routeInfo['method']}\n";
			}
		}

		// Create the file name
		$fileName = 'routermap_' . time() . '.yaml';

		// Write the YAML content to the file
		file_put_contents($fileName, $yamlContent);
	}
}
