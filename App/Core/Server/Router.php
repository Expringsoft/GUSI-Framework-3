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

	protected $compiledRoutes = array();

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
	public function addRoute(string $route, string|array $controller)
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

		// Route is compiled into a regex pattern
		$regex = $this->compileRoute($route);

		// Store the compiled route
		$this->compiledRoutes[] = [
			'regex' => $regex,
			'controller' => $controller,
		];

		// Optionally, keep the original route if needed
		$this->routes[$route] = $controller;

		return $this;
	}

	/**
	 * Compiles a route pattern into a regex.
	 *
	 * @param string $route The route pattern.
	 * @return string The compiled regex pattern.
	 */
	private function compileRoute(string $route)
	{
		// Special case for the root route '/' (direct request, eg. https://yoursite.com/).
		if ($route === '/') {
			return '#^/$#';
		}

		$regex = '';

		$routeSegments = explode('/', $route);

		foreach ($routeSegments as $segment) {
			if ($segment === '') continue; // Skip empty segments
			$regex .= '/'; // Add leading '/'
			if (preg_match('/^\{\@(.+)\}$/', $segment, $matches)) {
				// Wildcard parameter, match the rest of the URI
				$paramName = $matches[1];
				$regex .= '(?P<' . $paramName . '>.*)';
				break; // Wildcard captures rest, so we can stop processing segments
			} elseif (preg_match('/^\{(.+)\}$/', $segment, $matches)) {
				// Normal parameter
				$paramName = $matches[1];
				$regex .= '(?P<' . $paramName . '>[^/]+)';
			} else {
				// Literal segment
				$regex .= preg_quote($segment, '/');
			}
		}

		$regex = '#^' . $regex . '$#';

		return $regex;
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
		if (Configuration::LOCAL_ENVIRONMENT) {
			$this->baseUrl = $Protocol . $_SERVER['SERVER_NAME'] . Configuration::PATH_URL;
		} else {
			$this->baseUrl = $Protocol . Configuration::APP_DOMAIN;
		}
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
		return $_SERVER['REQUEST_URI'] ?? '';
	}

	/**
	 * Create a request object.
	 * 
	 * @return $this
	 */
	public function createRequest()
	{
		$uri = $this->getRequestUri();
		$uriPath = parse_url($uri, PHP_URL_PATH);

		// Remove leading and trailing slashes
		$uriPath = trim($uriPath, '/');

		// Store URI path segments
		$this->parameters['PATH_SEGMENTS'] = $uriPath === '' ? [] : explode('/', $uriPath);

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

		// Parse query string into GET parameters
		$queryString = parse_url($uri, PHP_URL_QUERY);
		if ($queryString) {
			parse_str($queryString, $queryParams);
			$this->parameters['GET'] = $queryParams;
		} else {
			$this->parameters['GET'] = [];
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
		$requestUri = $this->getRequestUri();
		$uriPath = parse_url($requestUri, PHP_URL_PATH);

		// Remove trailing slashes but keep leading slash
		$uriPath = '/' . trim($uriPath, '/');

		foreach ($this->compiledRoutes as $route) {
			$regex = $route['regex'];
			$controllerAndMethod = $route['controller'];

			if (preg_match($regex, $uriPath, $matches)) {
				$parameters = [];
				foreach ($matches as $key => $value) {
					if (!is_int($key)) {
						$parameters[$key] = $value;
					}
				}

				// Instantiate the controller with the method and parameters
				$controller = new $controllerAndMethod[0]($controllerAndMethod[1], $parameters);
				return;
			}
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
		foreach ($this->routes as $route => $controller) {
			$controllerName = $controller[0];
			$methodName = $controller[1];
			if (!isset($groupedRoutes[$controllerName])) {
				$groupedRoutes[$controllerName] = [];
			}
			$groupedRoutes[$controllerName][] = ['route' => $route, 'method' => $methodName];
		}
		$yamlContent = "Routermap:\n\n";
		foreach ($groupedRoutes as $controller => $routes) {
			$yamlContent .= "  {$controller}:\n";
			foreach ($routes as $routeInfo) {
				$yamlContent .= "    - route: {$routeInfo['route']} # {$routeInfo['method']}\n";
			}
		}
		$fileName = 'routermap_' . time() . '.yaml';
		file_put_contents($fileName, $yamlContent);
	}
}