<?php
namespace App\Core\Framework\Enumerables;

enum RequestMethods: string
{
	case GET = "GET";
	case POST = "POST";
	case PUT = "PUT";
	case DELETE = "DELETE";
	case PATCH = "PATCH";
	case OPTIONS = "OPTIONS";
	case HEAD = "HEAD";
	case CONNECT = "CONNECT";
	case TRACE = "TRACE";
	case ANY = "ANY";
}