<?php
namespace App\Core\Framework\Enumerables;

enum Channels{
	case PROD;
	case DEV;
	case TEST;
	case ALPHA;
	case BETA;
}