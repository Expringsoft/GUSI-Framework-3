<?php
namespace App\Core\Framework\Enumerables;

enum DataUnits{
	case BYTES;
	case KILOBYTES;
	case MEGABYTES;
	case GIGABYTES;
	case TERABYTES;
	case PETABYTES;
	case EXABYTES;
	case ZETTABYTES;
	case YOTTABYTES;
}