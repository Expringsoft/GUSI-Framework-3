<?php
namespace App\Core\Framework\Enumerables;

enum QueryTypes
{
	case SELECT;
	case INSERT;
	case UPDATE;
	case DELETE;
}