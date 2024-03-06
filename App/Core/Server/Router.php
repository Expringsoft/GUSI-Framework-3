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
	const CONTROLLER_CLASSNAME = 'Index';
	protected $controllerkey = 0;
	protected $baseUrl;
	protected $controllerClassName;

	protected $routes = array();

	protected $parameters = ['GET' => array(), 'POST' => array(), 'FETCH' => array(), 'PATH_SEGMENTS' => array()];

	/**
	 * Router constructor.
	 * 
	 * Initializes the Router object.
	 */
	public function __construct()
	{
		$this->controllerClassName = self::CONTROLLER_CLASSNAME;
		$this->setBaseUrl();
		$this->createRequest();
	}

	/**
	 * Adds a route to the router.
	 * 
	 * @param string $route The route.
	 * @param array $controller The controller and method.
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
	 * @return string The operating system from the user agent.  Unknown if not found.
	 */
	public static function getOSFromUserAgent()
	{
		$os = "Unknown";

		if (!isset($_SERVER['HTTP_USER_AGENT'])) {
			return $os;
		}

		$user_agent = $_SERVER['HTTP_USER_AGENT'];

		$os_array = array(
			'/windows nt 10/i'      =>  'Windows 10',
			'/windows nt 6.3/i'     =>  'Windows 8.1',
			'/windows nt 6.2/i'     =>  'Windows 8',
			'/windows nt 6.1/i'     =>  'Windows 7',
			'/windows nt 6.0/i'     =>  'Windows Vista',
			'/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
			'/windows nt 5.1/i'     =>  'Windows XP',
			'/windows xp/i'         =>  'Windows XP',
			'/windows nt 5.0/i'     =>  'Windows 2000',
			'/windows me/i'         =>  'Windows ME',
			'/win98/i'              =>  'Windows 98',
			'/win95/i'              =>  'Windows 95',
			'/win16/i'              =>  'Windows 3.11',
			'/iphone(?: ([0-9.,_]+))?/i' =>  'iPhone$1',
			'/macintosh|mac os x/i' =>  'Mac OS X',
			'/mac_powerpc/i'        =>  'Mac OS 9',
			'/android(?: ([0-9.]+))?/i'  =>  'Android$1',
			'/linux/i'              =>  'Linux',
			'/ubuntu/i'             =>  'Ubuntu',
			'/ipod/i'               =>  'iPod',
			'/ipad/i'               =>  'iPad',
			'/blackberry/i'         =>  'BlackBerry',
			'/webos/i'              =>  'Mobile'
		);

		foreach ($os_array as $regex => $value) {
			if (preg_match($regex, $user_agent, $matches)) {
				$os = str_replace('$1', isset($matches[1]) ? $matches[1] : '', $value);
				break;
			}
		}
		return $os;
	}

	/**
	 * Obtains the browser from the user agent.
	 *
	 * @return string The browser from the user agent.  Unknown if not found.
	 */
	public static function getBrowserFromUserAgent()
	{
		// Get the user agent (HTTP User Agent)
		$user_agent = $_SERVER['HTTP_USER_AGENT'];

		// Get the browser
		$browser = "Unknown";
		$browser_array = array(
			'/msie/i'       => 'Internet Explorer',
			'/edg/i'       => 'Microsoft Edge',
			'/edge/i'       => 'Microsoft Edge',
			'/firefox/i'    => 'Firefox',
			'/chrome/i'     => 'Chrome',
			'/safari/i'     => 'Safari',
			'/opera/i'      => 'Opera',
			'/netscape/i'   => 'Netscape',
			'/maxthon/i'    => 'Maxthon',
			'/konqueror/i'  => 'Konqueror',
			'/mobile/i'     => 'Dispositivo móvil'
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

	public function getBaseUrl()
	{
		return Configuration::LOCAL_ENVIRONMENT ? $_SERVER['SERVER_NAME'] . $this->baseUrl : $this->baseUrl;
	}

	public function setParameters($params)
	{
		$this->parameters = $params;
		return $this;
	}

	public function getParameters()
	{
		if ($this->parameters == null) {
			$this->parameters = array();
		}
		return $this->parameters;
	}

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

	public function getControllerClassName()
	{
		return $this->controllerClassName;
	}

	/**
	 * Obtiene valores de $_GET o $_POST. $_POST sobrescribe parámetros iguales de $_GET
	 * 
	 * @param type $name
	 * @param type $default
	 * @param type $filter
	 * @return type 
	 */
	public function getParam($name, $default = null)
	{
		if (isset($this->parameters[$name])) {
			return $this->parameters[$name];
		}
		return $default;
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

		/* 
		The following fix solves a bug where slashes "/" were being completely removed,
		causing getFormattedPathSegments() to always be "", and getControllerClassName()
		to return the entire URL without any separator slashes.
		*/
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

	public function createRequest()
	{
		$uri = $this->getRequestUri();
		$uriParts = explode('/', $uri);
		if (!isset($uriParts[$this->controllerkey])) {
			return $this;
		}
		$this->controllerClassName = $this->formatControllerName(explode("?", $uriParts[$this->controllerkey])[0]);

		unset($uriParts[$this->controllerkey]);

		// Obtener los datos recibidos via JSON, por ejemplo en solicitudes fetch que sean POST
		$jsonData = json_decode(file_get_contents("php://input"), true);

		// Si no hay datos JSON entonces crea un arreglo vacío
		if (!is_array($jsonData)) {
			$jsonData = [];
		}

		// Si no hay datos POST entonces crea un arreglo vacío
		if (!is_array($_POST)) {
			$_POST = [];
		}

		// Almacenar los datos recibidos via JSON y via POST
		$this->parameters['POST'] = array_merge($_POST, $jsonData);

		// Obtener y procesar la cadena de consulta
		$queryString = parse_url($uri, PHP_URL_QUERY);
		if ($queryString) {
			parse_str($queryString, $queryParams);
			foreach ($queryParams as $key => $value) {
				$this->parameters['GET'][$key] = $value;
			}
		}

		// Almacenar las partes de la URL sin las cadenas de consulta
		$this->parameters['PATH_SEGMENTS'] = [];
		foreach ($uriParts as $part) {
			$value = explode('?', $part)[0]; // Obtener solo el valor antes de '?'
			$this->parameters['PATH_SEGMENTS'][] = $value;
		}

		return $this;
	}

	/**
	 * return URL in lowercase
	 */
	protected function formatControllerName($unformatted)
	{
		return strtolower($unformatted);
	}

	/**
	 * Handles the incoming request.
	 * 
	 * @return void
	 */
	public function handleRequest()
	{
		$requestedRoute = $_SERVER['REQUEST_URI'];

		if (isset($this->routes[$requestedRoute])) {
			Logger::LogDebug(self::class, "Route '{$requestedRoute}' found.");
			$controllerName = $this->routes[$requestedRoute][0];
			$methodName = $this->routes[$requestedRoute][1];

			$controller = new $controllerName();
			//$controller->$methodName();
		} else {
			Logger::LogDebug(self::class, "Route '{$requestedRoute}' not found.");
			Actions::renderNotFound();
		}
	}
}
