<?php
namespace App\Core\Application;

/**
 * Class SharedConsts
 * 
 * This class contains shared constants for the application.
 */
class SharedConsts{
	public const EMPTY = "";
	public const SPACE = " ";
	public const COMMA = ",";
	public const DOT = ".";
	public const COLON = ":";
	public const SEMICOLON = ";";
	public const SLASH = "/";
	public const BACKSLASH = "\\";
	public const UNDERSCORE = "_";

	public const DEFAULT_LANGUAGE = "default";

	public const STR_VERSION_PARAM = "?version=";

	public const PATH_VIEW_NOT_FOUND = "Default/Not-Found.php";
	public const PATH_VIEW_ERROR = "Default/App-Error.php";

	#region HTTP ResponseCodes
	public const HTTP_RESPONSE_CONTINUE = 100;
	public const HTTP_RESPONSE_SWITCHING_PROTOCOLS = 101;
	public const HTTP_RESPONSE_PROCESSING = 102;
	public const HTTP_RESPONSE_EARLY_HINTS = 103;
	public const HTTP_RESPONSE_OK = 200;
	public const HTTP_RESPONSE_CREATED = 201;
	public const HTTP_RESPONSE_ACCEPTED = 202;
	public const HTTP_RESPONSE_NON_AUTHORITATIVE_INFORMATION = 203;
	public const HTTP_RESPONSE_NO_CONTENT = 204;
	public const HTTP_RESPONSE_RESET_CONTENT = 205;
	public const HTTP_RESPONSE_PARTIAL_CONTENT = 206;
	public const HTTP_RESPONSE_MULTI_STATUS = 207;
	public const HTTP_RESPONSE_ALREADY_REPORTED = 208;
	public const HTTP_RESPONSE_IM_USED = 226;
	public const HTTP_RESPONSE_MULTIPLE_CHOICES = 300;
	public const HTTP_RESPONSE_MOVED_PERMANENTLY = 301;
	public const HTTP_RESPONSE_FOUND = 302;
	public const HTTP_RESPONSE_SEE_OTHER = 303;
	public const HTTP_RESPONSE_NOT_MODIFIED = 304;
	public const HTTP_RESPONSE_USE_PROXY = 305;
	public const HTTP_RESPONSE_TEMPORARY_REDIRECT = 307;
	public const HTTP_RESPONSE_PERMANENT_REDIRECT = 308;
	public const HTTP_RESPONSE_BAD_REQUEST = 400;
	public const HTTP_RESPONSE_UNAUTHORIZED = 401;
	public const HTTP_RESPONSE_PAYMENT_REQUIRED = 402;
	public const HTTP_RESPONSE_FORBIDDEN = 403;
	public const HTTP_RESPONSE_NOT_FOUND = 404;
	public const HTTP_RESPONSE_METHOD_NOT_ALLOWED = 405;
	public const HTTP_RESPONSE_NOT_ACCEPTABLE = 406;
	public const HTTP_RESPONSE_PROXY_AUTHENTICATION_REQUIRED = 407;
	public const HTTP_RESPONSE_REQUEST_TIMEOUT = 408;
	public const HTTP_RESPONSE_CONFLICT = 409;
	public const HTTP_RESPONSE_GONE = 410;
	public const HTTP_RESPONSE_LENGTH_REQUIRED = 411;
	public const HTTP_RESPONSE_PRECONDITION_FAILED = 412;
	public const HTTP_RESPONSE_PAYLOAD_TOO_LARGE = 413;
	public const HTTP_RESPONSE_URI_TOO_LONG = 414;
	public const HTTP_RESPONSE_UNSUPPORTED_MEDIA_TYPE = 415;
	public const HTTP_RESPONSE_RANGE_NOT_SATISFIABLE = 416;
	public const HTTP_RESPONSE_EXPECTATION_FAILED = 417;
	public const HTTP_RESPONSE_IM_A_TEAPOT = 418;
	public const HTTP_RESPONSE_MISDIRECTED_REQUEST = 421;
	public const HTTP_RESPONSE_UNPROCESSABLE_ENTITY = 422;
	public const HTTP_RESPONSE_LOCKED = 423;
	public const HTTP_RESPONSE_FAILED_DEPENDENCY = 424;
	public const HTTP_RESPONSE_TOO_EARLY = 425;
	public const HTTP_RESPONSE_UPGRADE_REQUIRED = 426;
	public const HTTP_RESPONSE_PRECONDITION_REQUIRED = 428;
	public const HTTP_RESPONSE_TOO_MANY_REQUESTS = 429;
	public const HTTP_RESPONSE_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
	public const HTTP_RESPONSE_UNAVAILABLE_FOR_LEGAL_REASONS = 451;
	public const HTTP_RESPONSE_INTERNAL_SERVER_ERROR = 500;
	public const HTTP_RESPONSE_NOT_IMPLEMENTED = 501;
	public const HTTP_RESPONSE_BAD_GATEWAY = 502;
	public const HTTP_RESPONSE_SERVICE_UNAVAILABLE = 503;
	public const HTTP_RESPONSE_GATEWAY_TIMEOUT = 504;
	public const HTTP_RESPONSE_HTTP_VERSION_NOT_SUPPORTED = 505;
	public const HTTP_RESPONSE_VARIANT_ALSO_NEGOTIATES = 506;
	public const HTTP_RESPONSE_INSUFFICIENT_STORAGE = 507;
	public const HTTP_RESPONSE_LOOP_DETECTED = 508;
	public const HTTP_RESPONSE_NOT_EXTENDED = 510;
	public const HTTP_RESPONSE_NETWORK_AUTHENTICATION_REQUIRED = 511;
	#endregion
}